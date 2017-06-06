<?php namespace Octommerce\Octommerce\Components;

use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Order;
use Input;
use Flash;
use Redirect;

class OrderTracking extends ComponentBase
{

    public $orderNo;
    public $email;
    public $order;

    public function componentDetails()
    {
        return [
            'name'        => 'Track the order.',
            'description' => 'No description provided yet...'
        ];
    }

    public function onRun()
    {
        $this->orderNo = trim(strtoupper(Input::get('order_no')));
        $this->email = trim(Input::get('email'));

        if (!$this->orderNo || !$this->orderNo) {
            return;
        }

        $this->page['order'] = $this->order = Order::whereOrderNo($this->orderNo)->whereEmail($this->email)->first();

        if (!$this->order) {
            Flash::error('You have put invalid order data to be tracked');
        }
    }

}
