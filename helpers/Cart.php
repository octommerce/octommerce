<?php namespace Octommerce\Octommerce\Helpers;

use Auth;
use Session;
use Octommerce\Octommerce\Models\Cart as CartModel;
use Octommerce\Octommerce\Models\Product as ProductModel;
use RainLab\User\Models\User as UserModel;

/**
 * Cart helper
 */
class Cart
{

    public $cart;

    public function __construct()
    {
        $this->prepareVars();
    }

    public function addItem($productId, $qty = 1, $data = [])
    {
        $product = $this->getItem($productId);
        $cart = $this->get();

        $existingProduct = $this->findExistingItem($productId);

        if ($existingProduct) {
            $qty += $existingProduct->pivot->qty;
            $cart->products()->detach($productId);
        }

        if ($qty > 0) {
            $cart->products()->attach([
                $product->id => [
                    'qty' => $qty,
                    'discount' => 0, // temporary
                    'price' => $product->price,
                    'data' => $data ? json_encode($data) : null,
                ]
            ]);
        }

        // Get the latest update
        $cart = $this->get();

        return $cart;
    }

    public function updateItem($productId, $qty = null, $data = [])
    {
        $product = $this->getItem($productId);
        $cart = $this->get();

        $existingProduct = $this->findExistingItem($productId);

        if ($existingProduct) {
            $qty = $qty !== null ? $qty : $existingProduct->qty;
            $cart->products()->detach($productId);
        }

        if ($qty > 0) {
            $cart->products()->attach([
                $product->id => [
                    'qty' => $qty,
                    'discount' => 0, // temporary
                    'price' => $product->price,
                    'data' => $data ? json_encode($data) : null,
                ]
            ]);
        }

        // Get the latest update
        $cart = $this->get();

        return $cart;
    }

    public function removeItem($productId, $qty = 1)
    {
        $product = $this->getItem($productId);
        $cart = $this->get();

        $cart->products()->detach([$product->id]);

        $cart = $this->get();

        return $cart;
    }

    public function clear()
    {
        $cart = $this->get();

        $cart->products()->detach();

        $cart = $this->get();

        return $cart;
    }

    public function get($cartId = null)
    {
        if ($cartId) {
            return CartModel::find($cartId);
        }

        $cart = $this->cart = CartModel::firstOrCreate([
            'session_id' => Session::getId(),
        ]);

        // Attach user_id when user is logged in
        if (Auth::check()) {
            $cart->user_id = Auth::getUser()->id;
            $cart->save();
        }

        return $cart;
    }

    protected function prepareVars()
    {
        $this->get();
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