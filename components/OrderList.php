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
    public $orders;

    public function componentDetails()
    {
        return [
            'name'        => 'Order List',
            'description' => 'Displays a list of orders belonging to a user.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        if (! Auth::check()) {
            return $this->controller->run('404');;
        }

        $user = Auth::getUser();

        $this->orders = $this->page['orders'] = $user->orders;
    }
}
