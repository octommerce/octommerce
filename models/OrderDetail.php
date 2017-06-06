<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * OrderDetail Model
 */
class OrderDetail extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_order_product';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

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
        'order' => 'Octommerce\Octommerce\Models\Order',
        'product' => 'Octommerce\Octommerce\Models\Product'
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}
