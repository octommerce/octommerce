<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * Variation Model
 */
class Variation extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_variations';

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
    public $belongsToMany = [
        'products' => [
            'Octommerce\Octommerce\Models\Product',
            'table'    => 'octommerce_octommerce_products_variations',
        ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}
