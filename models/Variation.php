<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * Variation Model
 */
class Variation extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_variations';

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
    public $belongsTo = ['product' => [
            'Octommerce\Octommerce\Models\Product',
        ],];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function getSize()
    {
        return ['xxxl'=>'XXXL','xxl'=>'XXL','xl'=>'XL','l'=>'L','m'=>'M','s'=>'S'];
    }
}
