<?php namespace Octommerce\Octommerce;

use Event;
use System\Classes\PluginBase;
use Octommerce\Octommerce\Classes\ProductManager;
use Illuminate\Foundation\AliasLoader;
use Octommerce\Octommerce\Models\Category;

/**
 * Octommerce Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = ['RainLab.User', 'RainLab.Location', 'Responsiv.Currency', 'Responsiv.Pay'];

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

        /*
         * Register menu items for the RainLab.Pages plugin
         */
        Event::listen('pages.menuitem.listTypes', function() {
            return [
                'product-category' => 'Product Category',
                'all-product-categories' => 'All Product Categories'
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function($type) {
            if ($type == 'product-category' || $type == 'all-product-categories')
                return Category::getMenuTypeInfo($type);
        });

        Event::listen('pages.menuitem.resolveItem', function($type, $item, $url, $theme) {
            if ($type == 'product-category' || $type == 'all-product-categories')
                return Category::resolveMenuItem($item, $url, $theme);
        });
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register()
    {
        $alias = AliasLoader::getInstance();
        $alias->alias('Cart', 'Octommerce\Octommerce\Facades\Cart');
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

        ];
    }
}
