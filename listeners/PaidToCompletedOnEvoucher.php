<?php namespace Octommerce\Octommerce\Listeners;

use Mail;
use Octommerce\Octommerce\Models\OrderStatus;
use Octommerce\Octommerce\Models\OrderStatusLog;

/**
 * Jump order status from paid to completed if the product(s) only evoucher
 **/
class PaidToCompletedOnEvoucher 
{

    public function handle($invoice)
    {
        $order = $invoice->related;

        $products = $order->products->filter(function($product) {
            return $product->type == 'evoucher';
        });

        if ($products->count() < $order->products->count()) return;

        if ($status = OrderStatus::find('delivered')) {
            OrderStatusLog::createRecord($status, $order);
        }
    }

}
