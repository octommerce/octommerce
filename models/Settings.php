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
        'default_currency' => 'Responsiv\Currency\Models\Currency',
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];


    public function getCmsProductsPageOptions()
    {
        return $this->getAvailablePages('productList');
    }

    public function getCmsCategoryPageOptions()
    {
        return $this->getAvailablePages('productList');
    }

    public function getCmsBrandPageOptions()
    {
        return $this->getAvailablePages('productList');
    }

    public function getCmsListPageOptions()
    {
        return $this->getAvailablePages('productList');
    }

    public function getCmsProductDetailPageOptions()
    {
        return $this->getAvailablePages('productDetail');
    }

    public function getCmsCartPageOptions()
    {
        return Page::getNameList();
    }

    public function getCmsCheckoutPageAttribute($value)
    {
        return $value ?: array_get(array_keys($this->getCmsCheckoutPageOptions()), 1);
    }

    public function getCmsCheckoutPageOptions()
    {
        return $this->getAvailablePages('checkout');
    }

    public function getCmsPaymentPageAttribute($value)
    {
        return $value ?: array_get(array_keys($this->getCmsPaymentPageOptions()), 1);
    }

    public function getCmsPaymentPageOptions()
    {
        return $this->getAvailablePages('payment');
    }

    public function getCmsFinishPageOptions()
    {
        return $this->getAvailablePages('invoice');
    }

    public function getCmsOrdersPageOptions()
    {
        return $this->getAvailablePages('orderList');
    }

    public function getCmsOrderDetailPageOptions()
    {
        return $this->getAvailablePages('orderDetail');
    }

    protected function getAvailablePages($component)
    {
        $pages = Page::all();

        $result = ['' => '- Default - '];

        foreach ($pages as $page) {
            if (! $page->hasComponent($component))
                continue;

            $result[$page->baseFileName] = $page->title . ' (' . $page->baseFileName .')';
        }

        if (! count($result)) {
            return array_merge($result, Page::getNameList());
        }

        return $result;
    }
}