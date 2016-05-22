<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * Order Model
 */
class Order extends Model
{
    use \October\Rain\Database\Traits\SoftDeleting;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_orders';

    protected $dates = ['deleted_at'];

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
    public $hasMany = [
        'status_logs' => 'Ocotommerce\Octommerce\Models\OrderStatusLog',
    ];
    public $belongsTo = [];
    public $belongsToMany = [
        'products' => [
            'Ocotommerce\Octommerce\Models\Product',
            'table' => 'octommerce_octommerce_order_product',
        ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}