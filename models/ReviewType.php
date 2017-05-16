<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * review_type Model
 */
class ReviewType extends Model
{
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
}
