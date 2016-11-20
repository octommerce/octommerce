<?php namespace Octommerce\Octommerce\Components;

use Auth;
use Session;
use Currency;
use Exception;
use Cart as CartHelper;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Cart as CartModel;
use Octommerce\Octommerce\Models\Product;
use Octommerce\Octommerce\Models\Settings;

class Cart extends ComponentBase
{

    public $cart;
    public $settings;

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
        $this->settings = Settings::instance();
    }

    public function onAdd()
    {
        $cart = $this->page['cart'] = CartHelper::addItem(post('product_id'), post('qty') ?: 1);

        $this->page['settings'] = Settings::instance();

        return [
            'result' => 'Product successfully added to cart.',
            '.cart-counter' => $cart->count_qty,
            '.cart-subtotal' => Currency::format($cart->total_price, ['format' => 'short']),
        ];
    }

    public function onUpdate()
    {
        $cart = $this->page['cart'] = CartHelper::updateItem(post('product_id'), post('qty'));

        $this->page['settings'] = Settings::instance();

        return [
            'result' => 'Cart is successfully updated.',
            '.cart-counter' => $cart->count_qty,
            '.cart-subtotal' => Currency::format($cart->total_price, ['format' => 'short']),
        ];
    }

    public function onRemove()
    {
        $cart = $this->page['cart'] = CartHelper::removeItem(post('product_id'));

        $this->page['settings'] = Settings::instance();

        return [
            'result' => 'Product successfully removed from cart.',
            '.cart-counter' => $cart->count_qty,
            '.cart-subtotal' => Currency::format($cart->total_price, ['format' => 'short']),
        ];
    }

    public function onClear()
    {
        $cart = $this->page['cart'] = CartHelper::clear();

        $this->page['settings'] = Settings::instance();

        return [
            'result' => 'Cart is successfully cleared.',
            '.cart-counter' => $cart->count_qty,
            '.cart-subtotal' => Currency::format($cart->total_price, ['format' => 'short']),
        ];
    }

    public function onRefresh()
    {
        $cart = $this->page['cart'] = CartHelper::get();

        $this->page['settings'] = Settings::instance();

        // TODO:
        // Calculate stock availibity of every products.

        return [
            '.cart-counter' => $cart->count_qty,
            '.cart-subtotal' => Currency::format($cart->total_price, ['format' => 'short']),
        ];
    }

}