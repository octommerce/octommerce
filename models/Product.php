<?php namespace Octommerce\Octommerce\Models;

use Model;
use DB;
use Carbon\Carbon;
use Octommerce\Octommerce\Classes\ProductManager;

/**
 * Product Model
 */
class Product extends Model
{
    use \October\Rain\Database\Traits\SoftDeleting;
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SimpleTree;
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\Sortable;
    use \Nicolaslopezj\Searchable\SearchableTrait;

    protected $manager;

    public $productsCount;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_products';

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    /**
     * Validation rules
     */
    public $rules = [
        'name' => 'required',
        'price' => 'required|regex:/^(0+)?\d{0,10}(\.\d{0,2})?$/',
        'discount_price' => 'regex:/^(0+)?\d{0,10}(\.\d{0,2})?$/',
    ];

    /**
     * @var array Attributes that support translation, if available.
     */
    public $translatable = [
        'name',
        'description',
    ];

    /**
     * The attributes on which the post list can be ordered
     * @var array
     */
    public static $allowedSortingOptions = array(
        'name asc' => 'Name (ascending)',
        'name desc' => 'Name (descending)',
        'created_at asc' => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'price asc' => 'Price (ascending)',
        'price desc' => 'Price (descending)',
        'random' => 'Random',
        'sort_order asc' => 'Reordered (ascending)',
        'sort_order desc' => 'Reordered (descending)'
    );

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        'columns' => [
            'octommerce_octommerce_products.name' => 10,
            'octommerce_octommerce_products.keywords' => 8,
            'octommerce_octommerce_products.sku' => 5,
            'octommerce_octommerce_products.description' => 2,
        ],
        'joins' => [
            // 'octommerce_octommerce_category_product' => ['octommerce_octommerce_products.id', 'octommerce_octommerce_categories.product_id'],
        ],
    ];

    /**
     * @var array Generate slugs for these attributes.
     */
    protected $slugs = ['slug' => 'name'];

    /**
     * @var array Soft deletes.
     */
    protected $dates = ['deleted_at', 'available_from', 'available_to'];

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Jsonable fields
     */
    protected $jsonable = ['options'];

    /**
     * @var array Relations
     */
    public $hasOne = [
        'currency' => 'Octommerce\Octommerce\Models\Currency',
    ];

    public $hasMany = [
        'variations' => [
            'Octommerce\Octommerce\Models\Product',
            'key' => 'parent_id',
        ],
        'reviews' => 'Octommerce\Octommerce\Models\Review',
    ];

    public $belongsTo = [
        'parent'=> [
            'Octommerce\Octommerce\Models\Product',
            'key' => 'parent_id',
        ],
        'tax' => 'Octommerce\Octommerce\Models\Tax',
        'brand' => [
            'Octommerce\Octommerce\Models\Brand'
        ]
    ];

    public $belongsToMany = [
        'attributes' => [
            'Octommerce\Octommerce\Models\ProductAttribute',
            'table'    => 'octommerce_octommerce_product_product_attribute',
            'otherKey' => 'attribute_id',
            'pivot'    => ['value']
        ],

        'categories' => [
            'Octommerce\Octommerce\Models\Category',
            'table' => 'octommerce_octommerce_category_product',
        ],

        'up_sells' => [
            'Octommerce\Octommerce\Models\Product',
            'table' => 'octommerce_octommerce_product_up_sell',
            'key' => 'up_sell_id',
        ],

        'cross_sells' => [
            'Octommerce\Octommerce\Models\Product',
            'table' => 'octommerce_octommerce_product_cross_sell',
            'key' => 'cross_sell_id',
        ],

        'carts' => [
            'Octommerce\Octommerce\Models\Cart',
            'table' => 'octommerce_octommerce_cart_product',
        ],

        'orders' => [
            'Octommerce\Octommerce\Models\Order',
            'table' => 'octommerce_octommerce_order_product',
        ],

        'lists' => [
            'Octommerce\Octommerce\Models\ProductList',
            'table' => 'octommerce_octommerce_product_product_list',
            'otherKey' => 'list_id',
        ],
    ];

    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];

    public $attachMany = [
        'images' => ['System\Models\File'],
        'files' => ['System\Models\File'],
    ];

    public function __construct()
    {
        parent::__construct();

        $this->manager = ProductManager::instance();
    }

    public function getTypeOptions()
    {
        $list = [];

        foreach($this->manager->types as $type) {
            $list[$type['code']] = $type['name'];
        }

        return $list;
    }

    public function filterFields($fields, $context = null)
    {
        // Hide category on update
        if($context == 'update' && $this->parent) {
            isset($fields->type) ? $fields->type->hidden = true : '';
            isset($fields->categories) ? $fields->categories->hidden = true : '';
        }
    }

    /**
     * Allows filtering for specifc categories
     * @param  Illuminate\Query\Builder  $query      QueryBuilder
     * @param  array                     $categories List of category ids
     * @return Illuminate\Query\Builder              QueryBuilder
     */
    public function scopeFilterCategories($query, $categories)
    {
        return $query->whereHas('categories', function($q) use ($categories) {
            $q->whereIn('id', $categories);
        });
    }

    /**
     * Allows filtering for specifc lists
     * @param  Illuminate\Query\Builder  $query      QueryBuilder
     * @param  array                     $categories List of list ids
     * @return Illuminate\Query\Builder              QueryBuilder
     */
    public function scopeFilterLists($query, $lists)
    {
        return $query->whereHas('lists', function($q) use ($lists) {
            $q->whereIn('id', $lists);
        });
    }

    public function getAttributeIdOptions()
    {
        return ProductAttribute::lists('name', 'id');
    }

    // public function getSalePriceAttribute($value)
    // {
    //     if (!$value) {
    //         return $this->price;
    //     }
    // }

    public function beforeSave()
    {
        // Sale price must below the regular price
        if (! is_null($this->sale_price)) {
            $this->sale_price = $this->sale_price < $this->price ? $this->sale_price : null;
        }
    }

    public function scopeDisplayed($query)
    {
        // return $query->
    }

    public function isAvailable($qty = 1)
    {
        // Check stock management
        if (!$this->manage_stock) {
            return true;
        }

        if ($this->qty < $qty ||
            $this->available_from > Carbon::now() ||
            $this->available_to < Carbon::now()) {
            return false;
        }

        return true;
    }

    public function holdStock($qty)
    {
        //
    }

    public function releaseStock($qty)
    {
        //
    }

    public function inList($listSlug)
    {
        return in_array($listSlug, $this->lists->pluck('slug')->toArray());
    }

    public function getIsDiscountedAttribute()
    {
        if (!is_null($this->sale_price)) {
            return $this->sale_price < $this->price;
        }
        return false;
    }

    public function getDiscountPercentageAttribute()
    {
        return round(( 1 - $this->sale_price/$this->price) * 100);
    }

    public function getRelatedProductsAttribute()
    {

        $getCategories = $this->categories->lists('id');
        $relatedCategories = self::whereHas('categories', function($query) use ($getCategories) {
                        $query->whereIn('id', $getCategories);
                    })
                    ->where('id','<>',$this->id)
                    ->orderBy(DB::raw('RAND()'))
                    ->take(4)
                    ->get();

        $productsCount = $relatedCategories->count();
        //Get limit based on how much related categories have. Is it less than 4 or not?
        $limit = $productsCount < 4 ? 4 - $productsCount : 0;
        $related = self::where('id', '<>', $this->id)
                   ->orderBy(DB::raw('RAND()'))
                   ->take($limit)
                   ->get();

        return array_merge($relatedCategories->toArray(), $related->toArray());
    }
}
