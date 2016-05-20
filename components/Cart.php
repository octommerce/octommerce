<?php namespace Octommerce\Octommerce\Components;

use Session;
use Auth;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Cart as CartModel;
use Octommerce\Octommerce\Models\Product;

class Cart extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'cart Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {

      // dd(Session::getId());
        $cart = CartModel::whereSessionId(Session::getId())->first();

        $this->cart = $cart;
    }

    public function onAdd()
    {
        $user = Auth::getUser();
        $product = Product::find(post('id'));

        if (!$product) {
            throw new \ApplicationException('Product not found.');
        }

        $cart = CartModel::firstOrCreate([
            'session_id' => Session::getId(),
        ]);

        //attach user_id when user login
        $cart->user_id = Auth::check()?$user->id:null; $cart->save();

        $productData = [
            'width' => post('width'),
            'height' => post('height'),
            'material' => post('materialDetailInput'),
            'offset_top' => post('offset_top'),
            'offset_left' => post('offset_left'),
        ];



        $cart->products()->attach([
            $product->id => [
                'qty' => 1, // default to 1
                'discount' => $product->discount_price,
                'price' => $this->countOrder(), // temporary
                'data' => json_encode($productData),
            ]
        ]);

        $this->page['cart'] = $cart; // to render the partial

        return [
            'result' => 'Product successfully added to cart.',
            '#cartCounter' => $cart->products->count(),
        ];
    }

    public function onRemove()
    {
        $product = Product::find(post('product_id'));

        if (!$product) {
            throw new \ApplicationException('Product not found.');
        }

        $cart = CartModel::whereSessionId(Session::getId())->first();

        $cart->products()->detach([$product->id]);

        $this->page['cart'] = $cart; // to render the partial

        return [
            'result' => 'Product successfully removed from cart.',
            '#cartCounter' => $cart->products->count(),
        ];
    }

    public function countOrder() {
        $data = post();
        $price = $data['material'] * (($data['width'] * $data['height'])/10000);
        $discountPrice = $price * (Product::find($data['id'])->discount_price/100);
        $total_price = $price - $discountPrice;
        return $total_price;
    }

    public function onClear()
    {
        //
    }

}