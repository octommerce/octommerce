<?php namespace Octommerce\Octommerce\Components;

use Auth;
use Flash;
use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\PaymentConfirmation as Confirm;
use Octommerce\Octommerce\Models\Order;

class PaymentConfirmation extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'PaymentConfirmation Component',
            'description' => 'No description provided yet...'
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

            'successMessage' => [
                'title'       => 'Success Message',
                'description' => 'Success message if confirmation has done.',
                'type'        => 'text',
            ],
        ];
    }

    public function onRun()
    {

    }

    public function getRedirectPageOptions()
    {
        return Page::getNameList();
    }

    public function onConfirmPayment()
    {
        $data = post();
//        if(Auth::check()) {
            if($this->existAndValid($data['order_no'])) {

                try {
                    $confirm = Confirm::create($data);
                    $confirm->save();

                    Flash::success($this->property("successMessage"));
                    return Redirect::to(Page::url($this->property('redirectPage')));
                }
                catch(Exception $e) {
                    Db::rollBack();
                    throw new \ApplicationException($e->getMessage());
                }

            } else {

                throw new \ApplicationException("Your order no is not valid");

            }

//        } else {
//
//            throw new \ApplicationException("");
//
//        }

    }

    public function existAndValid($order_no)
    {
        if(!$order = Order::whereOrderNo($order_no)->first()) {
            return false;
        }

        $paymentMethod = "";

        foreach($order->invoices as $invoice) {
            $paymentMethod = $invoice->payment_method->name;
        }

        if($order->status_code == "waiting" && $paymentMethod == "Bank Transfer") {
            return true;
        }

        return false;

    }

}