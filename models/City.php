<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * City Model
 */
class City extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_cities';

    /**
     * Implement the CityModel behavior.
     */
    public $implement = ['Octommerce.Octommerce.Behaviors.CityModel'];

    public $timestamps = false;
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
        'state' => 'RainLab\Location\Models\State',
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}