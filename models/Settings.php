<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * Settings Model
 */
class Settings extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'octommerce_octommerce_settings';

    public $settingsFields = 'fields.yaml';

    /**
     * Validation rules
     */
    public $rules = [];

    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'base_location' => 'Octommerce\Octommerce\Models\City',
        'default_currency' => 'Octommerce\Octommerce\Models\Currency',
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}