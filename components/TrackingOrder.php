<?php namespace Octommerce\Octommerce\Components;

use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Order;
use Input;
use Flash;
use Redirect;

class TrackingOrder extends ComponentBase
{

    public $ordersDetail;

    public $order;

    public function componentDetails()
    {
        return [
            'name'        => 'Tracking Order',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [
            'orderNo' => [
                'title'       => 'Order ID',
                'description' => 'The ID of order',
                'default'     => '{{ :orderno }}',
                'type'        => 'text',
            ],
            'emailOrder' => [
                'title'       => 'Email Order',
                'description' => 'The email of order',
                'default'     => '{{ :email }}',
                'type'        => 'text',
            ],
            'redirectPage' => [
                'title'       => 'Redirect Page',
                'description' => 'Which page you will go?',
                'default'     => 'trackingorderform',
                'type'        => 'text',
            ],

        ];
    }

    public function onRun()
    {
        if($this->getOrder() != false) {
            $this->page['order'] = $this->order = $this->getOrder();
            $this->page['ordersDetail'] = $this->ordersDetail = $this->getOrder()->products;
        } else {
            Flash::error('You have put invalid order data to be tracked');
        }
    }

    public function getOrder()
    {
        $order = Order::whereOrderNo(Input::get('orderno'))->first();
        if($order) {
            $email = Input::get('email');
            if($order->email == $email) {
                return $order;
            }
        }
        return false;
    }

}
