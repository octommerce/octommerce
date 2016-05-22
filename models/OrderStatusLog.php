<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * OrderStatusLog Model
 */
class OrderStatusLog extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_order_status_logs';

    public $timestamps = false;

    protected $dates = ['timestamp'];

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    protected $jsonable = ['data'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'order' => 'Octommerce\Octommerce\Models\Order',
        'status' => 'Octommerce\Octommerce\Models\OrderStatus',
        'admin' => 'Backend\Models\User',
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}