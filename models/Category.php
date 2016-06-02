<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * Category Model
 */
class Category extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\NestedTree;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_categories';

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    /**
     * Validation rules
     */
    public $rules = [
        'name' => 'required',
    ];

    public $translatable = [
        'name',
        'description',
    ];

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
            'table' => 'octommerce_octommerce_category_product',
        ],
        'products_count' => [
            'Octommerce\Octommerce\Models\Product',
            'table' => 'octommerce_octommerce_category_product',
            'count' => true
        ],
    ];

    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];

    public $attachMany = [
        'images' => ['System\Models\File'],
    ];

}
