<?php namespace Octommerce\Octommerce\Models;

use Model;
use Octommerce\Octommerce\Models\Products;

/**
 * Brand Model
 */
class Brand extends Model
{
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_brands';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * Validation rules
     */
    public $rules = [
        'name' => 'required',
    ];

    /**
     * @var array Generate slugs for these attributes.
     */
    protected $slugs = ['slug' => 'name'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'products' => [
            'Octommerce\Octommerce\Models\Product',
        ],
        'products_count' => [
            'Octommerce\Octommerce\Models\Product',
            'count' => true
        ]
    ];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [
        'images' => ['System\Models\File'],
    ];

}