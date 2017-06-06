<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * Tag Model
 */
class Tag extends Model
{
    use \October\Rain\Database\Traits\Sluggable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_tags';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['name'];

    /**
     * @var array Generate slugs for these attributes.
     */
    protected $slugs = ['slug' => 'name'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [
        'products' => [
            'Octommerce\Octommerce\Models\Product',
            'table' => 'octommerce_octommerce_product_tag'
        ]
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}
