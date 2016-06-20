<?php namespace Octommerce\Octommerce\Classes;

use Db;
use Auth;
use Mail;
use Carbon\Carbon;
use RainLab\User\Models\User;
use Octommerce\Octommerce\Models\Order;
use Octommerce\Octommerce\Models\Cart;
use Octommerce\Octommerce\Models\City;
use Responsiv\Pay\Models\Invoice;
use Responsiv\Pay\Models\InvoiceItem;

class OrderManager
{
	use \October\Rain\Support\Traits\Singleton;

    public function create($data)
    {
        try {

            Db::beginTransaction();

            $cart = \Cart::get();

            if (!$cart) {
                throw new Exception('You have no item in cart.');
            }

            $user = $this->getOrRegisterUser($data);

            $order = new Order($data);

            $order->fill([
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'subtotal' => $cart->total_price,
            ]);

            if (isset($data['city_id']) && $cityId = $data['city_id']) {
                $city = City::find($cityId);

                if ($city) {
                    $order->city()->add($city);
                }

                if ($city->state) {
                    $order->state()->add($city->state);
                }
            }

            $order->user()->add($user);

            $order->save();

            foreach($cart->products as $product) {
                $order->products()->attach([
                    $product->id => [
                        'qty'      => $product->pivot->qty,
                        'price'    => $product->pivot->price,
                        'discount' => $product->pivot->discount,
                        'name'     => $product->name
                    ],
                ]);
            }

            $invoice = Invoice::create([
                'user_id'      => $user->id,
                'first_name'   => $order->name,
                'email'        => $order->email,
                'phone'        => $order->phone,
                'company'      => $order->company,
                'street_addr'  => $order->address,
                'city'         => $order->city ? $order->city->name : null,
                'zip'          => $order->postcode,
                'state_id'     => $order->state ? $order->state->id : null,
                'country_id'   => $order->state ? $order->state->country->id : null,
                'due_at'       => Carbon::now()->addDay(),
            ]);

            foreach($cart->products as $product) {
                $invoiceItem = new InvoiceItem([
                    'description' => $product->name,
                    'quantity' => $product->pivot->qty,
                    'price' => $product->pivot->price,
                    'discount' => $product->pivot->discount,
                ]);

                $invoice->items()->save($invoiceItem);
            }

            $order->invoices()->add($invoice);

            $invoice->save();

            \Cart::clear();

            Db::commit();

            return $order;
        }
        catch (Exception $e) {
            Db::rollBack();

            throw $e;
        }
    }

    protected function getOrRegisterUser($data, $update = true)
    {
        if (! Auth::check()) {

            if (User::whereEmail($data['email'])->count()) {
                throw new \ApplicationException('This email is already exist. Please login first.');
            }

            $data['password_confirmation'] = $data['password'] = $this->generateUserPassword();

            // Register, no need activation
            $user = Auth::register($data, true);

            // Logged in directly
            Auth::login($user);

            $this->sendPasswordUser($user, $data['password']);

        } else {
            $user = Auth::getUser();

            // Update data phone if any
            if($update) {
                $user->fill($data);
                $user->save();
            }
        }

        return $user;
    }

    public function checkExpiredOrders()
    {
        Order::whereStatus('pending')
            ->where('expired_at', '<=', Carbon::now())
            ->get()
            ->each(function($order) {

                // Set the order status to expired
                $order->status = 'expired';
                $order->save();

                // Get the redemption
                // $redemption = $order->coupon_redemption;

                // if($redemption) {
                //     // Release the coupon
                //     $redemption->release();
                // }

            });
    }

    protected function generateUserPassword($length = 8)
    {
        $characters = '123456789abcdefghijklmnpqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    protected function sendPasswordUser($user, $password) {
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
        ];

        Mail::send('octommerce.octommerce::mail.password_user', $data, function ($message) use ($user) {
            $message->to($user->email, $user->name);
        });
    }
}
