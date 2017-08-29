<?php namespace Octommerce\Octommerce\Models;

use Mail;
use Model;
use Octommerce\Octommerce\Models\Settings;

/**
 * Cart Model
 */
class Cart extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_carts';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];

    public $hasMany = [];

    public $belongsTo = [
        'user' => 'RainLab\User\Models\User',
    ];

    public $belongsToMany = [
        'products' => [
            'Octommerce\Octommerce\Models\Product',
            'table' => 'octommerce_octommerce_cart_product',
            'pivot' => ['qty', 'price', 'discount', 'data'],
        ]
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function getCountQtyAttribute()
    {
        $count = 0;

        foreach($this->products as $product) {
            $count += $product->pivot->qty;
        }

        return $count;
    }

    public function getSubtotalAttribute()
    {
        $subtotal = 0;

        foreach($this->products as $product) {
            $subtotal += ($product->pivot->qty * ($product->final_price - $product->pivot->discount));
        }

        return $subtotal;
    }

    public function getTotalPriceAttribute()
    {
        return $this->subtotal - $this->discount;
    }


    public function getTotalWeightAttribute()
    {
        $total = 0;

        foreach($this->products as $product) {

            $total += $product->pivot->qty * $product->weight;
        }

        return $total;
    }

    public function getIsAllowedCheckoutAttribute()
    {
        if (! $this->count_qty)
            return false;

        return $this->subtotal >= Settings::get('checkout_min_subtotal', 0);
    }

    public function getPreOrderProductsAttribute()
    {
        return $this->products->filter(function($product) {
            return $product->is_pre_order;
        });
    }

    public function getPreOrderShippingDateAttribute()
    {
        return $this->pre_order_products
            ->sortByDesc('preorder_shipping_date')
            ->first()
            ->preorder_shipping_date;
    }

    public function sendReminder()
    {
        if (!$this->user) {
            return;
        }

        $cart = $this;

        Mail::send('octommerce.octommerce::mail.abandoned_cart', compact('cart'), function($message) use ($cart) {
            $message->to($cart->user->email);
        });
    }
}
