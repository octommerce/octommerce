<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * OrderStatus Model
 */
class OrderStatus extends Model
{
    // use \October\Rain\Database\Traits\Sortable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_order_statuses';

    public $timestamps = false;

    public $primaryKey = 'code';

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
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}