<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * ProductList Model
 */
class ProductList extends Model
{

    use \October\Rain\Database\Traits\Sluggable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_product_lists';


    /**
     * @var array Generate slugs for these attributes.
     */
    protected $slugs = ['slug' => 'name'];

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
            'table' => 'octommerce_octommerce_product_product_list',
            'key' => 'list_id',
        ],
        'products_count' => [
            'Octommerce\Octommerce\Models\Product',
            'table' => 'octommerce_octommerce_product_product_list',
            'key' => 'list_id',
            'count' => true,
        ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [
        'images' => 'System\Models\File',
    ];

}