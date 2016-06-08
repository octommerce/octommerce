<?php namespace Octommerce\Octommerce\Components;

use Cms\Classes\ComponentBase;
use DB;
use Auth;
use Session;
use Carbon\Carbon;
use Mail;
use Flash;
use Input;
use RainLab\User\Models\User;
use Redirect;
use Octommerce\Octommerce\Models\Cart as CartModel;
use Octommerce\Octommerce\Models\Order as OrderModel;

class Checkout extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Checkout Orders',
            'description' => 'Checkout orders from cart'
        ];
    }

    public function defineProperties()
    {
         return [
            'token' => [
                'title'      =>  'Token',
                'type'       =>  'text',
                'default'    =>  ':token',
                'description'=>  'Token of payment url',
            ],
        ];
    }

    public function onSubmitOrder() {
        DB::beginTransaction();
        try {
            $post = post();
            // $this->getOrRegisterUser($post);
            if(Auth::check()) {
                $cartItems = CartModel::whereSessionId(Session::getId())->first();
                $cost = new Cost();
                $cost->onSelectCity();
                if(!$cartItems) {
                    return "You have no order";
                }else if($cost->onSelectCity()['shippingCost'] == 0) {
                    return "Sorry, you haven't selected appropriate destination city";
                }
                $user = $this->user = Auth::getUser();
                $order = new Order();
                $order->user_id        = $user->id;
                $order->name           = $user->name;
                $order->email          = $user->email;
                $order->phone          = $user->telephone;
                $order->address        = $user->address1;
                $order->postcode       = $user->postcode;
                $order->order_quantity = count($cartItems->products);
                $order->subtotal       = $cost->onSelectCity()['subTotal'];
                $order->shipping_cost  = $cost->onSelectCity()['shippingCost'];
                $order->insurance_cost = $cost->onSelectCity()['insuranceCost'];
                $order->expired_at     = Carbon::now()->addMinutes(60);
                $order->save();
                $counter = 0;
                foreach ($cartItems->products as $key => $product) {
                    $data = json_decode($product->pivot->data, true);
                    $textData = [
                        'char_sample' => $data['char_sample'],
                        //'char_length' => $data['char_length'],
                        'font' => $data['font'],
                        'font_size' => $data['font_size'],
                        'font_x' => $data['font_x'],
                        'font_y' => $data['font_y'],
                    ];
                    $order->products()->attach([
                        $product->id => [
                            'item' => $product->title,
                            'width' => $data['width'],
                            'height' => $data['height'],
                            'offset_top' => $data['offset_top'],
                            'offset_left' => $data['offset_left'],
                            'price' => $product->pivot->price,
                            'material' => $data['material'],
                            'discount' => $product->discount_price,
                            'text_data' => json_encode($textData),
                        ]
                    ]);
                    // $counter = $key+1;
                    // $arrayName = "item{$counter}_details";
                    $itemDetails[$key] = [
                        'id' => $product->id,
                        'price' => $product->pivot->price - $product->pivot->discount,
                        'quantity' => $product->pivot->qty,
                        'name'  => $product->title
                    ];
                    $counter+=1;
                }
                DB::commit();

                $cartItems->products()->detach();

                $itemDetails[$counter] = [
                    'id' => "shipping",
                    'price' => $order->shipping_cost,
                    'quantity' => 1,
                    'name' => 'Shipping Cost'
                ];

                if($order->insurance_cost > 0) {
                    $itemDetails[$counter+1] = [
                        'id' => "insurance",
                        'price' => $order->insurance_cost,
                        'quantity' => 1,
                        'name' => 'Insurance Cost'
                    ];
                }

                $customer_details = array(
                    'first_name'    => $user->name, //optional
                    'email'         => $user->email, //mandatory
                    'phone'         => $user->telephone, //mandatory
                    );

                $transactionData = array(
                        'order_id' => $order->invoice_no,
                        'gross_amount' => $order->subtotal + $order->shipping_cost + $order->insurance_cost
                );

                $transaction = array(
                    'transaction_details' => $transactionData,
                    'customer_details'    => $customer_details,
                    'item_details'        => $itemDetails
                );
                // return json_encode($transaction);
                $vtweb = new VTWeb();
                return $vtweb->onCheckout(json_encode($transaction));
            }

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

protected function getOrRegisterUser($data, $update = true)
    {
        if (! Auth::check()) {
            $dataUser['name'] = $data['name'];
            $dataUser['email'] = $data['email'];
            $dataUser['telephone'] = $data['telephone'];
            $dataUser['password'] = $this->generateRandomString(5);
            $dataUser['password_confirmation'] = $dataUser['password'];
            $dataUser['state_id'] = $data['state_id'];
            $dataUser['city_id'] = $data['city_id'];
            $dataUser['postcode'] = $data['postal_code'];
            $dataUser['address1'] = $data['address'];

            // Register, no need activation
            $user = Auth::register($dataUser, true);

            // Logged in directly
            Auth::login($user);

            $this->sendPasswordUser($user, $dataUser['password']);

        } else {
            $user = Auth::getUser();

            // Update data phone if any
            if($update && ($data['city_id'] || $data['state_id'] || $data['address1'] || $data['postcode'])) {
                // $user->telephone = $data['telephone'];
                $user->city_id = $data['city_id'];
                $user->state_id = $data['state_id'];
                $user->postcode = $data['postal_code'];
                $user->address1 = $data['address'];
                $user->save();
            }
        }

        return $user;
    }

    protected function generateRandomString($length = 5) {
        $characters = '123456789abcdefghijklmnpqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
