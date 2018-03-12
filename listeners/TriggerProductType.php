<?php namespace Octommerce\Octommerce\Listeners;

use Event;

class TriggerProductType 
{

    public function beforeAddToCart($cartHelper, $product, $cart, $qty, $data)
    {
        $product->type->beforeAddToCart($cart, $qty);
    }

    public function afterAddToCart($cartHelper, $product, $cart, $qty, $data)
    {
        $product->type->afterAddToCart($cart, $qty);
    }

    public function invoicePaid($invoice)
    {
        $order = $invoice->related;

        if ( ! $order instanceof \Octommerce\Octommerce\Models\Order) return;

        $order->products->each(function($product) use ($invoice) {
            return $product->type->invoicePaid($invoice);
        });

        Event::fire('octommerce.octommerce.productType.invoicePaidProcessed', [$invoice]);
    }

}
