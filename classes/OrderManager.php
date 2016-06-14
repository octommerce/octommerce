<?php namespace Octommerce\Octommerce\Classes;

use Db;
use Auth;
use Carbon\Carbon;
use Octommerce\Octommerce\Models\Order;
use Octommerce\Octommerce\Models\Cart;
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

            $order = new Order([
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'user_id' => $user->id,
                'subtotal' => $cart->total_price,
            ]);

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
                'user_id' => $user->id,
                'first_name' => $order->name,
                'email' => $order->email,
                'phone' => $order->phone,
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

            $data['password_confirmation'] = $data['password'] = $this->generateUserPassword();

            // Register, no need activation
            $user = Auth::register($data, true);

            // Logged in directly
            Auth::login($user);

            // $this->sendPasswordUser($user, $dataUser['password']);

        } else {
            $user = Auth::getUser();

            // Update data phone if any
            if($update && ($data['city_id'] || $data['state_id'] || $data['address'] || $data['postcode'])) {
                // $user->telephone = $data['telephone'];
                $user->city_id = $data['city_id'];
                $user->state_id = $data['state_id'];
                $user->postcode = $data['postcode'];
                $user->address = $data['address'];
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

}
