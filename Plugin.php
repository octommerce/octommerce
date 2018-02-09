<?php namespace Octommerce\Octommerce;

use Event;
use Yaml;
use File;
use Backend;
use Currency;
use System\Classes\PluginBase;
use Illuminate\Foundation\AliasLoader;
use Rainlab\User\Models\User;
use Rainlab\Location\Models\State;
use Rainlab\User\Controllers\Users as UsersController;
use Responsiv\Pay\Models\InvoiceStatus;
use Octommerce\Octommerce\Helpers\Cms;
use Octommerce\Octommerce\Classes\OrderManager;
use Octommerce\Octommerce\Classes\ProductManager;
use Octommerce\Octommerce\Models\Brand;
use Octommerce\Octommerce\Models\Order;
use Octommerce\Octommerce\Models\Category;
use Octommerce\Octommerce\Models\Product;
use Octommerce\Octommerce\Models\Settings;
use Octommerce\Octommerce\Models\OrderStatusLog;

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

            $model->hasMany['orders'] = [
                'Octommerce\Octommerce\Models\Order',
                'order' => 'created_at desc',
            ];
            $model->belongsTo['city'] = 'Octommerce\Octommerce\Models\City';
            $model->belongsTo['state'] = 'RainLab\Location\Models\State';
            $model->belongsToMany['expect_products'] = [
                'Octommerce\Octommerce\Models\Product',
                'table' => 'octommerce_octommerce_product_user'
            ];

            $model->addDynamicMethod('getSpendAttribute', function($value) use ($model) {
                $total = Order::where('user_id', $model->id)
                    ->whereIn('status_code', ['paid', 'shipped', 'packing', 'delivered'])
                    ->get()
                    ->sum('total');

                return Currency::format($total);
            });

            $model->addDynamicMethod('hasExpectProduct', function($productId) use ($model) {
                $user = User::where('id', $model->id)
                    ->whereHas('expect_products', function($query) use ($productId) {
                        return $query->where('product_id', $productId);
                    })->first();

                return !is_null($user);
            });

            $model->addDynamicMethod('getTransactionsAttribute', function($value) use ($model) {
                return Order::where('user_id', $model->id)
                    ->whereIn('status_code', ['paid', 'shipped', 'packing', 'delivered'])
                    ->get()
                    ->count();
            });

            $model->addDynamicMethod('getLastTransactionAttribute', function($value) use ($model) {
                $order = Order::where('user_id', $model->id)->orderBy('created_at', 'desc')->first();

                if ($order)
                    return $order->created_at->diffForHumans();
            });

        });

        /**
         * Add profile fields to backend users
         */
        UsersController::extendFormFields(function($form, $model, $context) {
            if(!$model instanceof User) return;
            $configFile = __DIR__ .'/config/profile_fields.yaml';
            $config = Yaml::parse(File::get($configFile));
            $form->addTabFields($config);
            $form->removeField('surname');
        });

        UsersController::extend(function($users) {
            $users->implement[] = 'Backend.Behaviors.RelationController';
            $users->relationConfig = '$/octommerce/octommerce/controllers/users/relationConfig.yaml';
        });

        State::extend(function($model) {
            $model->hasMany['users'] = [
                'Rainlab\User\Models\User'
            ];

            $model->hasMany['cities'] = [
                'Octommerce\Octommerce\Models\City'
            ];
        });

        \System\Controllers\Settings::extend(function($controller) {
            // Install CMS pages
            $controller->addDynamicMethod('onOctommerceCmsInstallPages', function() {
                $cms = Cms::instance();
                $cms->install();
            });
        });

        Event::listen('backend.list.extendColumns', function($widget) {

			// Only for the User controller
			if (!$widget->getController() instanceof \RainLab\User\Controllers\Users) {
				return;
			}

			// Only for the User model
			if (!$widget->model instanceof \RainLab\User\Models\User) {
				return;
			}

			// Add an extra birthday column
			$widget->addColumns([
                'spend' => [
                    'label'  => 'Spend',
                    'select' => '(select SUM(total) from `octommerce_octommerce_orders` where `octommerce_octommerce_orders`.`user_id` = `users`.`id`
                    and (
                        `octommerce_octommerce_orders`.`status_code` = "paid"
                        or `octommerce_octommerce_orders`.`status_code` = "shipped"
                        or `octommerce_octommerce_orders`.`status_code` = "packing"
                        or `octommerce_octommerce_orders`.`status_code` = "delivered"
                    ))'
                ],
                'transactions' => [
                    'label'  => 'Transactions',
                    'select' => '(select count(*) from `octommerce_octommerce_orders` where `octommerce_octommerce_orders`.`user_id` = `users`.`id`
                    and (
                        `octommerce_octommerce_orders`.`status_code` = "paid"
                        or `octommerce_octommerce_orders`.`status_code` = "shipped"
                        or `octommerce_octommerce_orders`.`status_code` = "packing"
                        or `octommerce_octommerce_orders`.`status_code` = "delivered"
                    ))'
                ],
                'last_transaction' => [
                    'label'  => 'Last Transaction',
                    'select' => '(select created_at from `octommerce_octommerce_orders` where `octommerce_octommerce_orders`.`user_id` = `users`.`id`
                    order by created_at desc limit 1
                    )'
                ]
			]);
        });

        // Global variable for settings
        Event::listen('cms.page.beforeDisplay', function($controller, $url, $page) {
            $controller->vars['octommerce_settings'] = Settings::instance();
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
                'product-category'       => 'Product Category',
                'all-product-categories' => 'All Product Categories',
                'all-brands'             => 'All Brands',
                'all-products'           => 'All Products',
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function($type) {
            if ($type == 'product-category' || $type == 'all-product-categories')
                return Category::getMenuTypeInfo($type);

            if ($type == 'all-brands')
                return Brand::getMenuTypeInfo($type);

            if ($type == 'all-products')
                return Product::getMenuTypeInfo($type);
        });

        Event::listen('pages.menuitem.resolveItem', function($type, $item, $url, $theme) {
            if ($type == 'product-category' || $type == 'all-product-categories')
                return Category::resolveMenuItem($item, $url, $theme);

            if ($type == 'all-brands')
                return Brand::resolveMenuItem($item, $url, $theme);

            if ($type == 'all-products')
                return Product::resolveMenuItem($item, $url, $theme);
        });

        // Update order status
        Event::listen('responsiv.pay.beforeUpdateInvoiceStatus', function($record, $invoice, $statusId, $previousStatus) {

            $newStatusCode = null;

            $oldStatusCode = InvoiceStatus::find($statusId)->code;

            switch($oldStatusCode) {
                case 'paid':
                case 'approved':
                    $newStatusCode = 'paid';
                    break;
                case 'void':
                    $newStatusCode = 'void';
                    break;
            }

            if ($newStatusCode) {
                //TODO: Check is $invoice->related Order model.

                $invoice->related->updateStatus($newStatusCode, $record->comment);
            }
        });
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register()
    {
        $alias = AliasLoader::getInstance();
        $alias->alias('Cart', 'Octommerce\Octommerce\Facades\Cart');

        $this->registerConsoleCommand('octommerce:dummy-product', 'Octommerce\Octommerce\Console\DummyProduct');
        $this->registerConsoleCommand('octommerce:delete-cart', 'Octommerce\Octommerce\Console\DeleteCart');
    }

    public function registerComponents()
    {
        return [
            'Octommerce\Octommerce\Components\ProductList'   => 'productList',
            'Octommerce\Octommerce\Components\ProductDetail' => 'productDetail',
            'Octommerce\Octommerce\Components\BrandList'     => 'brandList',
            'Octommerce\Octommerce\Components\CategoryList'  => 'categoryList',
            'Octommerce\Octommerce\Components\Cart'          => 'cart',
            'Octommerce\Octommerce\Components\Checkout'      => 'checkout',
            'Octommerce\Octommerce\Components\OrderDetail'   => 'orderDetail',
            'Octommerce\Octommerce\Components\OrderList'     => 'orderList',
            'Octommerce\Octommerce\Components\OrderTracking' => 'orderTracking',
            'Octommerce\Octommerce\Components\Review'        => 'review',
            'Octommerce\Octommerce\Components\Wishlist'      => 'wishlist',

            // Will be deprecated
            'Octommerce\Octommerce\Components\Account'       => 'OctommerceAccount',
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
            ],
            'holidays' => [
                'label'       => 'Holidays',
                'icon'        => 'icon-calendar',
                'description' => 'Configure holidays.',
                'url'         => Backend::url('octommerce/octommerce/holidays'),
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

	public function registerFormWidgets()
	{
		return [
			'Octommerce\Octommerce\FormWidgets\ProductList' => 'oc_productlist',
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
            'octommerce.octommerce::mail.stock_ready'          => 'Notify user if product stock ready',
            'octommerce.octommerce::mail.admin_new_order'      => 'Notify admin there is new order',
            'octommerce.octommerce::mail.admin_paid_order'     => 'Notify admin if user has been paid the order',
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
        $schedule->call(function() use($orderManager) {
            $orderManager->checkExpiredOrders();
        })->everyMinute();

        // Delete unused carts in a few days
        $schedule->command('octommerce:delete-cart')->daily();
    }
}
