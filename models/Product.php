<?php namespace Octommerce\Octommerce\Models;

use DB;
use Model;
use Carbon\Carbon;
use Cms\Classes\Theme;
use Cms\Classes\Page as CmsPage;
use Octommerce\Octommerce\Models\Settings;
use Octommerce\Octommerce\Classes\ProductManager;
use Octommerce\Octommerce\Observers\Product as ProductObserver;

/**
 * Product Model
 */
class Product extends Model
{
    use \October\Rain\Database\Traits\SoftDeleting;
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SimpleTree;
    use \October\Rain\Database\Traits\Sluggable;
    // use \October\Rain\Database\Traits\Sortable;
    use \Nicolaslopezj\Searchable\SearchableTrait;
    use \Octommerce\Octommerce\Traits\Sortable;
    use \Octommerce\Octommerce\Traits\Filterable;

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
        'priority' => 'numeric',
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
        'name asc'        => 'Name (ascending)',
        'name desc'       => 'Name (descending)',
        'created_at asc'  => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'price asc'       => 'Price (ascending)',
        'price desc'      => 'Price (descending)',
        'random'          => 'Random',
        'sort_order asc'  => 'Reordered (ascending)',
        'sort_order desc' => 'Reordered (descending)',
        'sales asc'       => 'Sales (ascending)',
        'sales desc'      => 'Sales (descending)'
    );

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        'columns' => [
            'octommerce_octommerce_products.name'        => 10,
            'octommerce_octommerce_products.keywords'    => 8,
            'octommerce_octommerce_brands.name'          => 5,
            'octommerce_octommerce_products.sku'         => 5,
            'octommerce_octommerce_products.description' => 2,
        ],
        'joins' => [
            'octommerce_octommerce_brands' => ['octommerce_octommerce_products.brand_id', 'octommerce_octommerce_brands.id'],
        ],
    ];

    /**
     * @var array Generate slugs for these attributes.
     */
    protected $slugs = ['slug' => 'name'];

    /**
     * @var array Soft deletes.
     */
    protected $dates = ['deleted_at', 'available_from', 'available_to', 'discount_start_at', 'discount_end_at'];

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
    public $hasOne = [];

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
        'brand' => [
            'Octommerce\Octommerce\Models\Brand'
        ],
        'currency' => [
            'Responsiv\Currency\Models\Currency',
            'key' => 'currency_code',
            'otherKey' => 'currency_code',
        ],
    ];

    public $belongsToMany = [
        'product_attributes' => [
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
            'key'   => 'up_sell_id',
        ],

        'cross_sells' => [
            'Octommerce\Octommerce\Models\Product',
            'table' => 'octommerce_octommerce_product_cross_sell',
            'key'   => 'cross_sell_id',
        ],

        'carts' => [
            'Octommerce\Octommerce\Models\Cart',
            'table' => 'octommerce_octommerce_cart_product',
        ],

        'orders' => [
            'Octommerce\Octommerce\Models\Order',
            'table' => 'octommerce_octommerce_order_product',
            'scope' => 'sales',
        ],

        'orders_count' => [
            'Octommerce\Octommerce\Models\Order',
            'table' => 'octommerce_octommerce_order_product',
            'scope' => 'sales',
            'count' => true
        ],

        'lists' => [
            'Octommerce\Octommerce\Models\ProductList',
            'table' => 'octommerce_octommerce_product_product_list',
            'otherKey' => 'list_id',
        ],
        'tags' => [
            'Octommerce\Octommerce\Models\Tag',
            'table' => 'octommerce_octommerce_product_tag'
        ]
    ];

    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];

    public $attachMany = [
        'images' => ['System\Models\File'],
        'files'  => ['System\Models\File'],
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->manager = ProductManager::instance();
    }

    public static function boot()
    {
        parent::boot();

        Product::observe(new ProductObserver);
    }

    public function getTypeOptions()
    {
        $list = [];

        foreach($this->manager->types as $type) {
            $list[$type['code']] = $type['name'];
        }

        return $list;
    }

    /**
     * Get tax options for dropdown tax field
     *
     * @return array taxes
     */
    public function getTaxOptions()
    {
        return collect(Settings::get('taxes'))->prepend([
                'name' => 'Default tax',
                'tax'  => Settings::get('default_tax')
            ])
            ->lists('name', 'tax');
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

    public function beforeSave()
    {
        // Set the sale price based on discount type
        switch ($this->discount_type) {
            case 'sale_price':
                // Sale price must below the regular price
                if (! is_null($this->sale_price)) {
                    $this->sale_price = $this->sale_price < $this->price ? $this->sale_price : null;
                }
                $this->discount_amount = null;
                break;
            case 'percentage':
                $this->sale_price = $this->price * ( 1 - $this->discount_amount / 100 );
                break;
            case 'fixed':
                $this->sale_price = $this->price - $this->discount_amount;
                break;
            default:
                $this->sale_price = null;
                $this->discount_amount = null;
        }

        $this->sale_price = $this->sale_price ? $this->calculateTax($this->sale_price) : null;
    }

    public function scopeAvailable($query)
    {
        return $query->where(function($query) {
            $query->where('manage_stock', false) // Where stock is unmanaged
                ->orWhere(function($query) {
                    $query->where('stock_status', '<>', 'out-of-stock') // Not ouf of stock
                        ->where(function($query) {
                            $query->where('qty', '>', 0);
                        });
                });
            });
    }

    public function scopePublished($query)
    {
        return $query->where('octommerce_octommerce_products.is_published', true);
    }

    public function scopeApplyPriority($query, $direction = 'desc')
    {
        return $query->orderBy('priority', $direction);
    }

    public function isAvailable($qty = 1)
    {
        // Check stock management
        if (!$this->manage_stock) {
            return true;
        }

        if ($this->stock_status == 'out-of-stock') {
            return false;
        }

        if ($this->qty === null) {
            return true;
        }

        if ($this->qty < $qty ||
            ($this->available_from && $this->available_from > Carbon::now()) ||
            ($this->available_to && $this->available_to < Carbon::now()) ) {
            return false;
        }

        return true;
    }

    public function holdStock($qty)
    {
        if ($this->manage_stock) {
            $this->qty -= $qty;
            $this->save();
        }
    }

    public function releaseStock($qty)
    {
        if ($this->manage_stock) {
            $this->qty += $qty;
            $this->save();
        }
    }

    public function inList($listSlug)
    {
        return in_array($listSlug, $this->lists->pluck('slug')->toArray());
    }

    public function getPageUrlAttribute()
    {
        return Settings::get('product_detail_page') ? CmsPage::url(Settings::get('product_detail_page'), ['slug' => $this->slug]) : null;
    }

    public function getSalePriceAttribute($value)
    {
        if (($this->discount_start_at && Carbon::now() < $this->discount_start_at) ||
            ($this->discount_end_at && Carbon::now() > $this->discount_end_at)) {
            return null;
        }

        return $value;
    }

    public function getFinalPriceAttribute()
    {
        // TODO:
        // if price is attached to user

        $price = is_null($this->sale_price) ? $this->price : $this->sale_price;

        // Calculate tax
        return $price * (1 + $this->tax / 100);
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
        $relatedCategories = self::published()
                    ->whereHas('categories', function($query) use ($getCategories) {
                        $query->whereIn('id', $getCategories);
                    })
                    ->where('id','<>',$this->id)
                    ->orderBy(DB::raw('RAND()'))
                    ->take(4)
                    ->get();

        $productsCount = $relatedCategories->count();
        //Get limit based on how much related categories have. Is it less than 4 or not?
        $limit = $productsCount < 4 ? 4 - $productsCount : 0;
        $related = self::published()
                   ->where('id', '<>', $this->id)
                   ->orderBy(DB::raw('RAND()'))
                   ->take($limit)
                   ->get();

        return $relatedCategories->merge($related);//array_merge($relatedCategories->toArray(), $related->toArray());
    }

    public function getIsLowStockAttribute()
    {
        if ($this->manage_stock && $this->stock_status == 'in-stock' && $this->qty != null) {
            return $this->qty <= Settings::get('low_stock_treshold');
        }

        return false;
    }

    public function isOutOfStockAttribute()
    {
        if ($this->stock_status == 'out-of-stock') {
            return true;
        }
    }

    public function getWeightAttribute($value)
    {
        if ($this->is_virtual)
            return 0;

        switch($this->weight_unit) {
            case 'gr':
                return $value;
            case 'kg':
                return $value * 1000;
            case 'ounce':
                return $value * 28.3495;
            case 'pound':
                return $value * 453.592;
        }
    }

    public function getTypeAttribute($value)
    {
        return ProductManager::instance()->findTypeByCode($value, $this);
    }

    /**
     * Handler for the pages.menuitem.getTypeInfo event.
     * Returns a menu item type information. The type information is returned as array
     * with the following elements:
     * - references - a list of the item type reference options. The options are returned in the
     *   ["key"] => "title" format for options that don't have sub-options, and in the format
     *   ["key"] => ["title"=>"Option title", "items"=>[...]] for options that have sub-options. Optional,
     *   required only if the menu item type requires references.
     * - nesting - Boolean value indicating whether the item type supports nested items. Optional,
     *   false if omitted.
     * - dynamicItems - Boolean value indicating whether the item type could generate new menu items.
     *   Optional, false if omitted.
     * - cmsPages - a list of CMS pages (objects of the Cms\Classes\Page class), if the item type requires a CMS page reference to
     *   resolve the item URL.
     * @param string $type Specifies the menu item type
     * @return array Returns an array
     */
    public static function getMenuTypeInfo($type)
    {
        $result = [];

        if ($type == 'all-products') {
            $result = [
                'dynamicItems' => true
            ];
        }

        if ($result) {
            $theme = Theme::getActiveTheme();

            $pages = CmsPage::listInTheme($theme, true);
            $cmsPages = [];
            foreach ($pages as $page) {
                if (!$page->hasComponent('productDetail')) {
                    continue;
                }

                /*
                 * Component must use a category filter with a routing parameter
                 * eg: brandFilter = "{{ :somevalue }}"
                 */
                $properties = $page->getComponentProperties('productDetail');
                if (!isset($properties['slug']) || !preg_match('/{{\s*:/', $properties['slug'])) {
                    continue;
                }

                $cmsPages[] = $page;
            }

            $result['cmsPages'] = $cmsPages;
        }

        return $result;
    }

    /**
     * Handler for the pages.menuitem.resolveItem event.
     * Returns information about a menu item. The result is an array
     * with the following keys:
     * - url - the menu item URL. Not required for menu item types that return all available records.
     *   The URL should be returned relative to the website root and include the subdirectory, if any.
     *   Use the URL::to() helper to generate the URLs.
     * - isActive - determines whether the menu item is active. Not required for menu item types that
     *   return all available records.
     * - items - an array of arrays with the same keys (url, isActive, items) + the title key.
     *   The items array should be added only if the $item's $nesting property value is TRUE.
     * @param \RainLab\Pages\Classes\MenuItem $item Specifies the menu item.
     * @param \Cms\Classes\Theme $theme Specifies the current theme.
     * @param string $url Specifies the current page URL, normalized, in lower case
     * The URL is specified relative to the website root, it includes the subdirectory name, if any.
     * @return mixed Returns an array. Returns null if the item cannot be resolved.
     */
    public static function resolveMenuItem($item, $url, $theme)
    {
        $result = null;

        if ($item->type == 'all-products') {
            $result = [
                'items' => []
            ];

            $products = self::published()->get();
            foreach ($products as $product) {
                $productItem = [
                    'title' => $product->name,
                    'url'   => self::getProductPageUrl($item->cmsPage, $product, $theme),
                    'mtime' => $product->updated_at,
                ];

                $productItem['isActive'] = $productItem['url'] == $url;

                $result['items'][] = $productItem;
            }
        }

        return $result;
    }

    /**
     * Returns URL of a product page.
     */
    protected static function getProductPageUrl($pageCode, $product, $theme)
    {
        $page = CmsPage::loadCached($theme, $pageCode);
        if (!$page) return;

        $properties = $page->getComponentProperties('productDetail');
        if (!isset($properties['slug'])) {
            return;
        }

        /*
         * Extract the routing parameter name from the product filter
         * eg: {{ :someRouteParam }}
         */
        if (!preg_match('/^\{\{([^\}]+)\}\}$/', $properties['slug'], $matches)) {
            return;
        }

        $paramName = substr(trim($matches[1]), 1);
        $url = CmsPage::url($page->getBaseFileName(), [$paramName => $product->slug]);

        return $url;
    }
}
