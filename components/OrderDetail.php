<?php namespace Octommerce\Octommerce\Components;

use Auth;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Order;

class OrderDetail extends ComponentBase
{
    public $order;

    public function componentDetails()
    {
        return [
            'name'        => 'orderDetail Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [
            'order_no' => [
                'title'       => 'Order ID',
                'description' => 'The ID of order',
                'default'     => '{{ :order_no }}',
                'type'        => 'text',
            ],
        ];
    }

    public function onRun()
    {
        if (! Auth::check()) {
            return $this->controller->run('404');
        }

        $this->order = $this->page['order'] = $order = $this->getOrder();

        if (! $order) {
            return $this->controller->run('404');
        }
    }

    protected function getOrder()
    {
        $user = Auth::getUser();

        return Order::with(['user', 'status_logs', 'products'])
            ->whereOrderNo($this->property('order_no'))
            ->whereUserId($user->id)
            ->first();
    }

}