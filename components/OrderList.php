<?php namespace Octommerce\Octommerce\Components;

use Auth;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Order as OrderModel;
use ApplicationException;

class OrderList extends ComponentBase
{

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
    }

    protected function loadOrders()
    {
        if (!$user = Auth::getUser()) {
            throw new ApplicationException('You must be logged in');
        }

        $orders = OrderModel::whereUserId($user->id)->orderBy('created_at', 'desc')->get();

        // $orders->each(function($order) {
        //     $order->setUrlPageName($this->orderPage);
        // });

        return $orders;
    }

}