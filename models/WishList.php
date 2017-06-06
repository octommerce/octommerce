<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * WishList Model
 */
class WishList extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_wish_lists';

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
        'product' => ['Octommerce\Octommerce\Models\Product'],
        'user'    => ['RainLab\User\Models\User'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}
