<?php namespace Octommerce\Octommerce\Helpers;

use Auth;
use Event;
use Session;
use Octommerce\Octommerce\Models\Cart as CartModel;
use Octommerce\Octommerce\Models\Product as ProductModel;
use RainLab\User\Models\User as UserModel;

/**
 * Cart helper
 */
class Cart
{
    use \October\Rain\Support\Traits\Emitter;

    public $cart;

    public function __construct()
    {
        $this->prepareVars();
    }

    public function addItem($productId, $qty = 1, $data = [])
    {
        $product = $this->getItem($productId);
        $cart = $this->get();

        /*
         * Extensibility
         */
        if (
            ($this->fireEvent('cart.beforeAddItem', [$product, $cart, $qty, $data], true) === false) ||
            (Event::fire('cart.beforeAddItem', [$this, $product, $cart, $qty, $data], true) === false)
        ) {
            return;
        }

        $existingProduct = $this->findExistingItem($productId);

        if ($existingProduct) {
            $qty += $existingProduct->pivot->qty;
        }

        if (!$product->isAvailable($qty)) {
            throw new \ApplicationException('No more stock.');
        }

        $cart->products()->detach($productId);

        if ($qty > 0) {
            $cart->products()->attach([
                $product->id => [
                    'qty' => $qty,
                    'discount' => 0, // temporary
                    'price' => $product->getPrice(),
                    'data' => $data ? json_encode($data) : null,
                ]
            ]);
        }

        // Reload the products relation
        $cart->reloadRelations('products');

        /*
         * Extensibility
         */
        $this->fireEvent('cart.afterAddItem', [$product, $cart, $qty, $data]);
        Event::fire('cart.afterAddItem', [$this, $product, $cart, $qty, $data]);

        // Get the latest update
        $cart->reload()->reloadRelations();

        return $cart;
    }

    public function updateItem($productId, $qty = null, $data = [])
    {
        $product = $this->getItem($productId);
        $cart = $this->get();

        /*
         * Extensibility
         */
        if (
            ($this->fireEvent('cart.beforeUpdateItem', [$product, $cart, $qty, $data], true) === false) ||
            (Event::fire('cart.beforeUpdateItem', [$this, $product, $cart, $qty, $data], true) === false)
        ) {
            return;
        }

        $existingProduct = $this->findExistingItem($productId);

        if ($existingProduct) {
            $qty = $qty !== null ? $qty : $existingProduct->qty;
        }

        if (!$product->isAvailable($qty)) {
            throw new \ApplicationException('No more stock.');
        }

        $cart->products()->detach($productId);

        if ($qty > 0) {
            $cart->products()->attach([
                $product->id => [
                    'qty' => $qty,
                    'discount' => 0, // temporary
                    'price' => $product->getPrice(),
                    'data' => $data ? json_encode($data) : null,
                ]
            ]);
        }

        // Reload the products relation
        $cart->reloadRelations('products');

        /*
         * Extensibility
         */
        $this->fireEvent('cart.afterUpdateItem', [$product, $cart, $qty, $data]);
        Event::fire('cart.afterUpdateItem', [$this, $product, $cart, $qty, $data]);

        // Get the latest update
        $cart->reload()->reloadRelations();

        return $cart;
    }

    public function removeItem($productId, $qty = 1)
    {
        $product = $this->getItem($productId);
        $cart = $this->get();

        /*
         * Extensibility
         */
        if (
            ($this->fireEvent('cart.beforeRemoveItem', [$product, $cart, $qty], true) === false) ||
            (Event::fire('cart.beforeRemoveItem', [$this, $product, $cart, $qty], true) === false)
        ) {
            return;
        }

        $cart->products()->detach([$product->id]);

        // Reload the products relation
        $cart->reloadRelations('products');

        /*
         * Extensibility
         */
        $this->fireEvent('cart.afterRemoveItem', [$product, $cart, $qty]); //, $data
        Event::fire('cart.afterRemoveItem', [$this, $product, $cart, $qty]); //, $data

        // Get the latest update
        $cart->reload()->reloadRelations();

        return $cart;
    }

    public function clear()
    {
        $cart = $this->get();

        $cart->products()->detach();

        // Reload the products relation
        $cart->reloadRelations('products');

        return $cart;
    }

    public function destroy()
    {
        $cart = $this->get();

        $cart->delete();
    }

    public function get($cartId = null)
    {
        // If cart id
        if ($cartId) {
            return CartModel::find($cartId);
        }

        // If logged in, check by user_id
        if (Auth::check()) {
            $cart = CartModel::whereUserId(Auth::getUser()->id)->first();
        }

        // If not found, find by session_id
        if (!isset($cart) || !$cart) {
            $cart = CartModel::whereSessionId(Session::getId())->first();
        }

        if (isset($cart) && $cart) {
            // Attach user_id to cart
            if (Auth::check() && !$cart->user_id) {
                $cart->user_id = Auth::getUser()->id;
                $cart->save();
            }
            // Remove session id if user_id is already attached
            if ($cart->session_id && $cart->user_id) {
                $cart->session_id = null;
                $cart->save();
            }
        }
        // If not found, create a new one
        else {
            // If logged in, attach to user_id
            if (Auth::check()) {
                $cart = CartModel::create([
                    'user_id' => Auth::getUser()->id,
                    'session_id' => '',
                ]);
            }
            // If not logged in, attach to session_id
            else {
                $cart = CartModel::create([
                    'session_id' => Session::getId(),
                ]);
            }
        }

        return $cart;
    }

    protected function prepareVars()
    {
        $this->cart = $this->get();
    }

    // protected function getUser($userId = null)
    // {
    //     if ($userId) {
    //         return UserModel::find($userId);
    //     }
    // }

    protected function getItem($productId)
    {
        $product = ProductModel::find($productId);

        if (!$product) {
            throw new \ApplicationException('Product not found.');
        }

        // TODO:
        // Check is available for sale

        return $product;
    }

    protected function findExistingItem($productId)
    {
        return $this->cart->products->filter(function($product) use($productId) {
            return $product->id == $productId;
        })->first();
    }


}