<?php namespace Octommerce\Octommerce\Components;

use Auth;
use Flash;
use Input;
use Redirect;
use Cms\Classes\Page;
use ApplicationException;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Order as OrderModel;

class OrderList extends ComponentBase
{
    public $orders;

    public function componentDetails()
    {
        return [
            'name'        => 'octommerce.octommerce::lang.component.order_list.name',
            'description' => 'octommerce.octommerce::lang.component.order_list.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'ordersPerPage' => [
                'title'             => 'octommerce.octommerce::lang.component.order_list.param.orders_per_page_title',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'octommerce.octommerce::lang.component.order_list.param.orders_per_page_validation_message',
                'default'           => '10',
                'group'             => 'Pagination',
            ],
        ];
    }

    public function onRun()
    {
        if (! Auth::check()) {
            return $this->controller->run('404');;
        }

        $user = Auth::getUser();

        $page = Input::get('page') ?: 1;

        $this->orders = $this->page['orders'] = $user->orders()->paginate($this->property('ordersPerPage'), $page);
    }
}
