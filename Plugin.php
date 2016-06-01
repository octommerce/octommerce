<?php namespace Octommerce\Octommerce;

use System\Classes\PluginBase;
use Octommerce\Octommerce\Classes\ProductManager;

/**
 * Octommerce Plugin Information File
 */
class Plugin extends PluginBase
{

    public function boot()
    {
        $productManager = ProductManager::instance();

        //
        // Built in Types
        //

        $productManager->registerTypes([
            'Octommerce\Octommerce\ProductTypes\Simple',
            'Octommerce\Octommerce\ProductTypes\Variable',
        ]);

        \Octommerce\Octommerce\Controllers\Products::extendFormFields(function($form, $model, $context) use($productManager) {
            if (!$model instanceof \Octommerce\Octommerce\Models\Product)
                return;

            $productManager->addCustomFields($form);

        });
    }

    public function register()
    {
        //
    }

    public function registerComponents()
    {
        return [
            'Octommerce\Octommerce\Components\Cart'          => 'cart',
            'Octommerce\Octommerce\Components\ProductList'   => 'productList',
            'Octommerce\Octommerce\Components\ProductDetail' => 'productDetail',
        ];
    }

    public function registerSettings()
    {
        return [
            'config' => [
                'label'       => 'Octommerce',
                'icon'        => 'icon-shopping-cart',
                'description' => 'Configure Octommerce plugins.',
                'class'       => 'Octommerce\Octommerce\Models\Settings',
                'permissions' => ['octommerce.octommerce.manage_plugins'],
                'order'       => 60
            ]
        ];
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'currency' => [$this, 'currencyFormat'],
            ],
        ];
    }

    public function currencyFormat($value, $pretty = false, $currency = 'IDR')
    {
        $value = number_format($value);

        if($pretty && strlen($value) > 4) {
            $value = '<span class="price-highlight">' . substr_replace($value, '</span>', strlen($value) - 4, 0);
        }

        return $currency . ' ' . $value;
    }

}
