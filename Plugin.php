<?php namespace Octommerce\Octommerce;

use Event;
use System\Classes\PluginBase;
use Octommerce\Octommerce\Classes\ProductManager;
use Illuminate\Foundation\AliasLoader;
use Octommerce\Octommerce\Models\Category;
use Octommerce\Octommerce\Models\Brand;
use Rainlab\Location\Models\State;
use Rainlab\User\Models\User;

/**
 * Octommerce Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = ['RainLab.User', 'RainLab.Location', 'Responsiv.Currency', 'Responsiv.Pay'];

    public function boot()
    {
        //
        // Extend RainLab.Location
        //
        State::extend(function($model) {
            $model->hasMany['cities'] = [
                'Octommerce\Octommerce\Models\City'
            ];
        });

        //
        // Extend RainLab.User
        //
        User::extend(function($model) {

            $model->addFillable([
                'phone',
                'country_id',
                'state_id',
                'city_id',
                'address',
                'postcode',
            ]);

            $model->hasMany['orders'] = ['Octommerce\Octommerce\Models\Order'];
            $model->belongsTo['city'] = 'Octommerce\Octommerce\Models\City';
            $model->belongsTo['state'] = 'Rainlab\Location\Models\State';
        });

        State::extend(function($model) {
            $model->hasMany['users'] = [
                'Rainlab\User\Models\User'
            ];

            $model->hasMany['cities'] = [
                'Octommerce\Octommerce\Models\City'
            ];
        });

        //
        // Built in Types
        //
        $productManager = ProductManager::instance();

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
                'all-product-categories' => 'All Product Categories',
                'all-brands' => 'All Brands',
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function($type) {
            if ($type == 'product-category' || $type == 'all-product-categories')
                return Category::getMenuTypeInfo($type);

            if ($type == 'all-brands')
                return Brand::getMenuTypeInfo($type);
        });

        Event::listen('pages.menuitem.resolveItem', function($type, $item, $url, $theme) {
            if ($type == 'product-category' || $type == 'all-product-categories')
                return Category::resolveMenuItem($item, $url, $theme);

            if ($type == 'all-brands')
                return Brand::resolveMenuItem($item, $url, $theme);
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
            'Octommerce\Octommerce\Components\ProductList'   => 'productList',
            'Octommerce\Octommerce\Components\ProductDetail' => 'productDetail',
            'Octommerce\Octommerce\Components\ProductSearch' => 'productSearch',
            'Octommerce\Octommerce\Components\Cart'          => 'cart',
            'Octommerce\Octommerce\Components\Checkout'      => 'checkout',
            'Octommerce\Octommerce\Components\Account'      => 'OctommerceAccount',
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

    public function registerReportWidgets()
    {
        return [
            'Octommerce\Octommerce\ReportWidgets\Summary' => [
                'label'   => 'E-commerce Summary',
                'context' => 'aa'
            ]
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'octommerce.octommerce::mail.new_order'            => 'Confirmation email on new order.',
            'octommerce.octommerce::mail.payment_reminder'     => 'Reminder email for payment.',
            'octommerce.octommerce::mail.paid_order'           => 'Paid order notification to customer.',
            'octommerce.octommerce::mail.expired_order'        => 'Expired order notification to customer.',
            'octommerce.octommerce::mail.packing_order'        => 'Packing order notification to customer.',
            'octommerce.octommerce::mail.shipped_order'        => 'Shipping status notification to customer.',
            'octommerce.octommerce::mail.delivered_order'      => 'Delivery status notification to customer.',
            'octommerce.octommerce::mail.cancelled_order'      => 'Cancelled order notification to customer.',
            'octommerce.octommerce::mail.refunded_order'       => 'Refunded order notification to user.',
            'octommerce.octommerce::mail.abandoned_cart'       => 'Abandoned cart reminder to customer.',
            'octommerce.octommerce::mail.product_availibility' => 'Notification when a product is available.',
            'octommerce.octommerce::mail.backend_order'        => 'Order notification to backend users.',
            'octommerce.octommerce::mail.backend_low_stock'    => 'Low stock notification to backend users.',
            'octommerce.octommerce::mail.forgot_password'      => 'Forgot password link',
        ];
    }

    public function registerSchedule($schedule)
    {
        $orderManager = OrderManager::instance();

        // Check abandoned carts, waiting payments every hour
        $schedule->call(function() use($orderManager) {
            // Abandoned carts
            $orderManager->remindAbandonedCarts();
            // Waiting for payments
            $orderManager->remindWaitingPayments();
        })->hourly();

        // Check expired orders every minute
	/*
	$schedule->call(function() use($orderManager) {
            $orderManager->checkExpiredOrders();
        })->everyMinute();
	*/
   }
}
