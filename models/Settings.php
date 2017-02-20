<?php namespace Octommerce\Octommerce\Models;

use Model;
use Cms\Classes\Page;
use Cms\Classes\Theme;

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


    public function getProductsPageOptions() {
        return Page::getNameList();
    }

    public function getCategoryPageOptions() {
        return Page::getNameList();
    }

    public function getProductDetailPageOptions() {
        return Page::getNameList();
    }

    public function getCartPageOptions() {
        return Page::getNameList();
    }

    public function getCheckoutPageOptions() {
        return Page::getNameList();
    }

}