<?php namespace Octommerce\Octommerce\Classes;

use Carbon\Carbon;
use Octommerce\Octommerce\Models\Order;
use Octommerce\Octommerce\Models\Cart;

class OrderManager
{
	use \October\Rain\Support\Traits\Singleton;

    public function create($data)
    {
        $order = new Order;

				$orderDetail = $order->products;

				$cart = Cart::whereSessionId(Session::getId())->first();

        $order->name = $data['name'];

				$order->email = $data['email'];

				$order->phone = $data['phone'];

				$order->subtotal = $cart->total_price;

				foreach ($cart->products as $product) {
            $order->products()->attach([
                $product->id => [
                    'qty' 			=> $product->pivot->qty,
										'price' 		=> $product->pivot->price,
										'discount'  => $product->pivot->discount,
										'name'			=> $product->name
                ]
            ]);
				}

        $order->save();

        //TODO:
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

}
