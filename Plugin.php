<?php namespace Octommerce\Octommerce;

use System\Classes\PluginBase;

/**
 * Octommerce Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Octommerce',
            'description' => 'No description provided yet...',
            'author'      => 'Trias Nur Rahman',
            'icon'        => 'icon-shopping-cart'
        ];
    }

    public function register()
    {
        //
    }

    public function registerComponents()
    {
        return [
            //
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

    public function registerNavigation()
    {
        return [
            'products' => [
                'label'       => 'Products',
                'url'         => \Backend::url('octommerce/octommerce/products'),
                'icon'        => 'icon-tags',
                'permissions' => ['octommerce.octommerce.*'],
                'order'       => 30,

                'sideMenu' => [
                    'products' => [
                        'label'       => 'All Products',
                        'icon'        => 'icon-barcode',
                        'url'         => \Backend::url('octommerce/octommerce/products'),
                        'permissions' => ['octommerce.octommerce.access_products']
                    ],

                    'attributes' => [
                        'label'       => 'Attributes',
                        'icon'        => 'icon-flag',
                        'url'         => \Backend::url('octommerce/octommerce/attributes'),
                        'permissions' => ['octommerce.octommerce.access_products']
                    ],

                    'categories' => [
                        'label'       => 'Categories',
                        'icon'        => 'icon-table',
                        'url'         => \Backend::url('octommerce/octommerce/categories'),
                        'permissions' => ['octommerce.octommerce.access_products']
                    ],

                    'lists' => [
                        'label'       => 'Lists',
                        'icon'        => 'icon-reorder',
                        'url'         => \Backend::url('octommerce/octommerce/lists'),
                        'permissions' => ['octommerce.octommerce.access_products']
                    ],

                ]
            ],

            'order' => [
                'label'       => 'Orders',
                'url'         => \Backend::url('octommerce/octommerce/orders'),
                'icon'        => 'icon-shopping-cart',
                'permissions' => ['octommerce.octommerce.*'],
                'order'       => 40,
            ],
        ];
    }

}
