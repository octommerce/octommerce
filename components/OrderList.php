<?php namespace Octommerce\Octommerce\Components;

use Auth;
use Redirect;
use Flash;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Order as OrderModel;
use ApplicationException;

class OrderList extends ComponentBase
{

    public $orderPage;
    public $orders;
    public $ordersDetail;

    public function componentDetails()
    {
        return [
            'name'        => 'Order List',
            'description' => 'Displays a list of orders belonging to a user.'
        ];
    }

    public function defineProperties()
    {
        return [
            'orderPage' => [
                'title'       => 'Order page',
                'description' => 'Name of the invoice page file for the invoice links. This property is used by the default component partial.',
                'type'        => 'dropdown',
            ],

            'orderNo' => [
                'title'       => 'Order ID',
                'description' => 'The ID of order',
                'default'     => '{{ :orderno }}',
                'type'        => 'text',
            ],
        ];
    }

    public function getInvoicePageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->orderPage = $this->page['orderPage'] = $this->property('orderPage');
        $this->orders = $this->page['orders'] = $this->loadOrders();
        $this->order = $this->page['order'] = OrderModel::whereOrderNo($this->property('orderNo'))->first();
        if($this->order) {
            if($this->order->user_id !== $this->user()->id) {
                Flash::error("Order No is not valid");
                return Redirect::to('account/orders');
            }
            $this->ordersDetail = $this->page['ordersDetail'] = $this->loadOrdersDetail();
        }
    }

    public function user()
    {
        if(!Auth::check()) {
            return null;
        }

        return Auth::getUser();
    }

    protected function loadOrders()
    {
        if (!$user = $this->user()) {
            // throw new ApplicationException('You must be logged in');
            Flash::get("You must be logging in");
            return Redirect::to('/');
        }

        return $user->orders;

    }

    protected function loadOrdersDetail()
    {
        if (!$user = $this->user()) {
            Flash::error("You must be logging in");
            return Redirect::to('login');
            // throw new ApplicationException('You must be logged in');
        } else {
            if($order = OrderModel::whereOrderNo($this->property('orderNo'))->first()) {
                if($order->user_id == $this->user()->id) {
                    // throw new ApplicationException('Order no is not valid');
                    $ordersDetail = $order->products;
                    return $ordersDetail;
                }
            }
        }

        return null;
    }

}
