<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * reviewItem Model
 */
class ReviewItem extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_review_items';

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
    public $belongsTo = [
        'review' => 'Octommerce\Octommerce\Models\Review',
        'reviewType' => 'Octommerce\Octommerce\Models\ReviewType'
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
}
