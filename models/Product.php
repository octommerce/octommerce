<?php namespace Octommerce\Octommerce\Models;

use Model;
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

    protected $manager;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_products';

    /**
     * Validation rules
     */
    public $rules = [
        'name' => 'required',
        'price' => 'required|regex:/^(0+)?\d{0,10}(\.\d{0,2})?$/',
        'discount_price' => 'regex:/^(0+)?\d{0,10}(\.\d{0,2})?$/',
    ];

    /**
     * @var array Generate slugs for these attributes.
     */
    protected $slugs = ['slug' => 'name'];

    /**
     * @var array Soft deletes.
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

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
        // parent::construct();

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

    public function getAttributeIdOptions()
    {
        return ProductAttribute::lists('name', 'id');
    }

}
