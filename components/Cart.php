<?php namespace Octommerce\Octommerce\Components;

use Cart as CartHelper;
use Session;
use Exception;
use Auth;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Cart as CartModel;
use Octommerce\Octommerce\Models\Product;

class Cart extends ComponentBase
{

    public $cart;

    public function componentDetails()
    {
        return [
            'name'        => 'Cart Component',
            'description' => 'Use it on every page.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {

        $this->cart = CartModel::whereSessionId(Session::getId())->first();
    }

    public function onAdd()
    {
        $cart = $this->page['cart'] = CartHelper::addItem(post('product_id'), post('qty') ?: 1);

        return [
            'result' => 'Product successfully added to cart.',
            '.cart-counter' => $cart->products->count(),
        ];
    }

    public function onUpdate()
    {
        $cart = $this->page['cart'] = CartHelper::updateItem(post('product_id'), post('qty'));

        return [
            'result' => 'Cart is successfully updated.',
            '.cart-counter' => $cart->products->count(),
        ];
    }

    public function onRemove()
    {
        $cart = $this->page['cart'] = CartHelper::removeItem(post('product_id'));

        return [
            'result' => 'Product successfully removed from cart.',
            '.cart-counter' => $cart->products->count(),
        ];
    }

    public function onClear()
    {
        $cart = $this->page['cart'] = CartHelper::clear();

        return [
            'result' => 'Cart is successfully cleared.',
            '.cart-counter' => $cart->products->count(),
        ];
    }

}