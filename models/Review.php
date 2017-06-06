<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * Review Model
 */
class Review extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_reviews';

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
    public $hasMany = [
        'review_items' => 'Octommerce\Octommerce\Models\ReviewItem'
                ];
    public $belongsTo = [
        'product' => 'Octommerce\Octommerce\Models\Product',
        'user' => 'RainLab\User\Models\User',
        'order' => 'Octommerce\Octommerce\Models\Order'
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}