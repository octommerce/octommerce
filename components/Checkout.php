<?php namespace Octommerce\Octommerce\Components;

use Db;
use Auth;
use Mail;
use Flash;
use Input;
use Exception;
use Session;
use Redirect;
use Carbon\Carbon;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use RainLab\User\Models\User;
use Octommerce\Octommerce\Classes\OrderManager;
use Octommerce\Octommerce\Models\Cart as CartModel;
use Octommerce\Octommerce\Models\Order as OrderModel;
use Responsiv\Pay\Models\Invoice;
use Responsiv\Pay\Models\InvoiceItem;

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
            'token' => [
                'title'      =>  'Token',
                'type'       =>  'text',
                'default'    =>  ':token',
                'description'=>  'Token of payment url',
            ],
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

    // public function __construct()
    // {
    //     parent::__construct();
    //
    //     $this->orderManager = OrderManager::instance();
    // }

    public function onSubmitOrder()
    {
        $data = post();

        $this->orderManager->create($data);

        //return Redirect::to($this->property('redirectPage'));

        return Redirect::to('/');
    }

    public function onSubmit()
    {

        try {

            Db::beginTransaction();
            //$this->getOrRegisterUser();

            $cart = \Cart::get();

            if (!$cart) {
                throw new Exception('You have no item in cart.');
            }

            $data = post();

            $order = new OrderModel();
            $order->fill([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'subtotal' => $cart->total_price,
            ]);

            $order->save();

            foreach($cart->products as $product) {
                $order->products()->attach([
                    $product->id => [
                        'qty'      => $product->pivot->qty,
                        'price'    => $product->pivot->price,
                        'discount' => $product->pivot->discount,
                        'name'     => $product->name
                    ],
                ]);
            }

           //TODO:
            // - Create user
            // - Create invoice from Responsiv.Pay Plugin
            //   and then get the hash for redirection

            $invoice = Invoice::create([
                'first_name' => $order->name,
                'email' => $order->email,
                'phone' => $order->phone,
            ]);

            foreach($cart->products as $product) {
                $invoiceItem = InvoiceItem::create([
                    'description' => $product->name,
                    'quantity' => $product->pivot->qty,
                    'price' => $product->pivot->price,
                    'discount' => $product->pivot->discount,
                ]);

                $invoiceItem->invoice()->associate($invoice);
                $invoiceItem->save();
            }

            $invoice->save();

            \Cart::clear();

            Db::commit();
        }
        catch (Exception $e) {
            Db::rollBack();

            throw new \ApplicationException($e->getMessage());
        }

        return Redirect::to(Page::url($this->property('redirectPage'), ['hash' => $invoice->hash]));
    }

protected function getOrRegisterUser($data, $update = true)
    {
        if (! Auth::check()) {
            $dataUser['name'] = $data['name'];
            $dataUser['email'] = $data['email'];
            $dataUser['phone'] = $data['phone'];
            $dataUser['password'] = $this->generateRandomString(5);
            $dataUser['password_confirmation'] = $dataUser['password'];
            $dataUser['state_id'] = $data['state_id'];
            $dataUser['city_id'] = $data['city_id'];
            $dataUser['postcode'] = $data['postal_code'];
            $dataUser['address1'] = $data['address'];

            // Register, no need activation
            $user = Auth::register($dataUser, true);

            // Logged in directly
            Auth::login($user);

            $this->sendPasswordUser($user, $dataUser['password']);

        } else {
            $user = Auth::getUser();

            // Update data phone if any
            if($update && ($data['city_id'] || $data['state_id'] || $data['address1'] || $data['postcode'])) {
                // $user->telephone = $data['telephone'];
                $user->city_id = $data['city_id'];
                $user->state_id = $data['state_id'];
                $user->postcode = $data['postal_code'];
                $user->address1 = $data['address'];
                $user->save();
            }
        }

        return $user;
    }

    protected function generateRandomString($length = 5) {
        $characters = '123456789abcdefghijklmnpqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
