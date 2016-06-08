<?php namespace Octommerce\Octommerce\Classes;

use Carbon\Carbon;
use Octommerce\Octommerce\Models\Order;

class OrderManager
{
	use \October\Rain\Support\Traits\Singleton;

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