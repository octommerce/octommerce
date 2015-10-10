<?php namespace Octommerce\Octommerce\Models;

use Model;

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
        // 'user' => '',
    ];

    public $belongsToMany = [
        'products' => 'Octommerce\Octommerce\Models\Product',
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}
