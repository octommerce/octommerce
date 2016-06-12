<?php namespace Octommerce\Octommerce\Models;

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

    public function getTotalPriceAttribute()
    {
        $total = 0;

        foreach($this->products as $product) {
            $total += ($product->pivot->qty * ($product->pivot->price - $product->pivot->discount));
        }

        return $total;
    }

    public function getIsAllowedCheckoutAttribute()
    {
        return $this->total_price >= Settings::get('checkout_min_subtotal', 0);
    }
}
