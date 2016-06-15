<?php namespace Octommerce\Octommerce\Components;

use Db;
use Cart as CartHelper;
use Flash;
use Exception;
use Redirect;
use Cms\Classes\Page;
use Cms\Classes\CodeBase;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Classes\OrderManager;

class Checkout extends ComponentBase
{
    public $orderManager;

    public function componentDetails()
    {
        return [
            'name'        => 'Checkout Orders',
            'description' => 'Checkout orders from cart'
        ];
    }

    public function defineProperties()
    {
         return [
            'redirectPage' => [
                'title'       => 'Redirect Page',
                'description' => 'What page when the order is successfully submitted.',
                'type'        => 'dropdown',
            ],
        ];
    }

    public function getRedirectPageOptions()
    {
        return Page::getNameList();
    }

    public function __construct(CodeBase $cmsObject = null, $properties = [])
    {
        parent::__construct($cmsObject, $properties);

        $this->orderManager = OrderManager::instance();
    }

    public function onRun()
    {
        $cart = CartHelper::get();

        if (! $cart->is_allowed_checkout) {
            Flash::error('Your cart is not allowed for checkout.');
            $this->setStatusCode(404);
            return $this->controller->run('404');
        }
    }

    public function onSubmit()
    {
        $data = post();

        try {
            $order = $this->orderManager->create($data);
        }
        catch(Exception $e) {
            throw new \ApplicationException($e->getMessage());
        }

        return Redirect::to(Page::url($this->property('redirectPage'), ['hash' => $order->invoices->last()->hash]));
    }
}
