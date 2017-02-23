<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * ProductAttribute Model
 */
class ProductAttribute extends Model
{
    use \October\Rain\Database\Traits\Purgeable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_product_attributes';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array List of attributes to purge.
     */
    protected $purgeable = ['_default', '_options'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    protected $jsonable = ['options'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [
        'products' => [
            'Octommerce\Octommerce\Models\Product',
            'table'    => 'octommerce_octommerce_product_product_attribute',
            'key'      => 'attribute_id',
            'pivot'    => ['value']
        ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}