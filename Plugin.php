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
            'Octommerce\Octommerce\Components\Cart'        => 'cart',
            'Octommerce\Octommerce\Components\ProductList' => 'productList',
        ];
    }

    public function registerPermissions()
    {
        return [
            //
        ];
    }

    public function registerMarkupTags()
    {
        return [
            //
        ];
    }

}
