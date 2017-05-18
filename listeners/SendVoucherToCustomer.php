<?php namespace Octommerce\Octommerce\Listeners;

use Mail;
use Octommerce\Octommerce\Classes\ProductManager;

class SendVoucherToCustomer 
{

    public function handle($invoice)
    {
        $order = $invoice->related;

        $products = $order->products->filter(function($product) {
            return $product->type == 'evoucher';
        });

        if ( ! $products->count()) return;

        Mail::send('octommerce.octommerce::mail.evoucher_list', compact('order', 'products'), function($message) use ($order) {
            $message->to($order->email, $order->name);
        });
    }

}
