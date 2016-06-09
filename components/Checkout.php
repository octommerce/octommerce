<?php namespace Octommerce\Octommerce\Components;

use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Classes\OrderManager;
use DB;
use Auth;
use Session;
use Carbon\Carbon;
use Mail;
use Flash;
use Input;
use RainLab\User\Models\User;
use Redirect;
use Octommerce\Octommerce\Models\Cart as CartModel;
use Octommerce\Octommerce\Models\Order as OrderModel;

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
                'type'        => 'string',
            ],
        ];
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

    public function onSubmit() {

        $order = new OrderModel();

        //$this->getOrRegisterUser();

        if(!$cart = CartModel::whereSessionId(Session::getId())->first()) {
            return "You have no orders.";
        }

        $data = post();

        $order->name = $data['name'];

				$order->email = $data['email'];

				$order->phone = $data['phone'];

				$order->subtotal = $cart->total_price;

        $order->save();

				foreach($cart->products as $product) {
            $order->products()->attach([
                $product->id => [
                    'qty' 			=> $product->pivot->qty,
										'price' 		=> $product->pivot->price,
										'discount'  => $product->pivot->discount,
										'name'			=> $product->name
                ]
            ]);
				}

        $cart->products()->detach();
        return Redirect::to($this->property('redirectPage'));
        //TODO:
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
