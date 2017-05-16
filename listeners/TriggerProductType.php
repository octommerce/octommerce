<?php namespace Octommerce\Octommerce\Listeners;

use Octommerce\Octommerce\Classes\ProductManager;

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

}
