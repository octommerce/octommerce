<?php namespace Octommerce\Octommerce\Models;

use Model;
use Octommerce\Octommerce\Classes\ProductManager;
/**
 * reviewType Model
 */
class ReviewType extends Model
{
    private $manager;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_review_types';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];


    protected $jsonable = ['product_type_codes'];
    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'review_items' => 'Octommerce\Octommerce\Models\ReviewItem'
    ];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->manager = ProductManager::instance();
    }

    public function getProductTypeCodesOptions()
    {
        // $list = [];

        foreach($this->manager->types as $type) {
            $list[$type['code']] = $type['name'];
        }

        return $list;
    }
}
