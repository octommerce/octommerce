<?php namespace Octommerce\Octommerce\Components;

use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Classes\OrderManager;

class Order extends ComponentBase
{
    public $order;

    protected $orderManager;

    public function componentDetails()
    {
        return [
            'name'        => 'order Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [
            'redirectPage' => [
                'title'       => 'Redirect Page',
                'description' => 'What page when the order is successfully submitted.',
                'type'        => 'string',
            ],
        ];
    }

    public function __construct()
    {
        parent::__construct();

        $this->orderManager = OrderManager::instance();
    }

    public function onRun()
    {
        //
    }

    public function onSubmit()
    {
        $data = post();

        $this->orderManager->create($data);

        return Redirect::to($this->property('redirectPage'));
    }

}