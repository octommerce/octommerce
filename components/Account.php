<?php namespace Octommerce\Octommerce\Components;

//use Cms\Classes\ComponentBase;
use Lang;
use Auth;
use Mail;
use URL;
use Flash;
use Input;
use Request;
use Redirect;
use Validator;
use Exception;
use ValidationException;
use ApplicationException;
use Cms\Classes\Page;
use Hash;
use RainLab\User\Models\Settings as UserSettings;
use Rainlab\User\Components\Account as AccountModel;
use Rainlab\User\Models\User as UserModel;
use Rainlab\Location\Models\State;
use Octommerce\Octommerce\Models\City;

class Account extends AccountModel
{

    public function componentDetails()
    {
        return [
            'name'        => 'Account',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [
            'forgotToken' => [
                'title'       => 'Forgot Password Token',
                'description' => 'Forgot password code token of user',
                'default'     => '{{ :token }}',
                'type'        => 'text',
            ],
        ];
    }


    public function onRun()
    {
        $this->getProfileRegion();
    }

    public function getAllStates() {
        // return CountryModel::with('states')->orderBy('name', 'ASC')->get();
        return State::whereHas('country', function($query) {
            $query->whereName('Indonesia');
        })->get();

    }



    /**
     * Retrieve all cities.
     *
     * @return
     */
    public function getAllCities()
    {
        return City::orderBy('name', 'ASC')->get();
    }



    /**
     * Update the user
     * @override
     */
    public function onUpdate()
    {
        if (!$user = $this->user()) {
            return;
        }

        $user->fill(post());
        $user->save();


        Flash::success(post('flash', Lang::get('rainlab.user::lang.account.success_saved')));

        /*
         * Redirect
         */
        if ($redirect = $this->makeRedirection()) {
            return $redirect;
        }
    }

    public function onUpdateForgotPassword() {
        $data = post();
        $user = UserModel::whereResetPasswordCode($this->property('forgotToken'))->first();
        $user->fill($data);
        $user->reset_password_code = "";
        $user->save();
        Flash::success("Berhasil Merubah Password Anda");
        Auth::login($user);

        return Redirect::intended("/");
    }

    public function onChangePassword() {
        if(Auth::check()) {
            $oldPassword = $this->user()->password;
            if(!Hash::check(post('oldPassword'), $oldPassword)) {
                Flash::error("Password lama anda salah");
            }
            else {
                $data = post();
                $user = $this->user();
                $user->fill($data);
                if($user->save()) {
                    Flash::success("Password baru berhasil disimpan, silahkan masuk kembali dengan password baru");
                    return Redirect::to('login');
                }
            }
        }
    }

    public function onForgotPassword()
    {
        $data = post();
        $user = UserModel::where("email",$data["email"])->first();
        if(!$user){
            Flash::error("Email Tidak Terdaftar");
            return Redirect::refresh();
        }else{

            $user->reset_password_code =  $this->generateRandomString(200);
            $user->update();

            $data = [
                'name' =>  $user->name,
                'link' =>  URL::to("forgot-password",$user->reset_password_code)  ,
                'code' =>  $user->reset_password_code
            ];

            Mail::send('octommerce.octommerce::mail.forgot_password', $data, function($message) use ($user) {
                $message->to($user->email, $user->name);
            });


            Flash::success("Email Pemberitahuan telah dikirimkan ke email ".$user->email);
            return Redirect::intended("/");
        }
    }

    public function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function getOrders() {
        if(!Auth::check()) {
            return [];
        }

        $orders = OrderModel::where('user_id', Auth::getUser()->id)->orderBy('created_at','desc')->get();
        return $orders;
    }

    public function getOrderDetails() {
        if(!Auth::check()) {
            return [];
        }
        $orders = OrderModel::whereToken($this->property('paramOrder'))->first();
        return $orders;
    }

    // function onSelectCountry() {
    //     $this->page['states'] = CountryModel::find(post('getIdCountry'))->states;
    // }

    function onSelectState() {
        $this->page['cities'] = State::find(post('state_id'))->cities;
    }

    function onSelectShippingState() {
        $this->page['shippingCities'] = State::find(post('shipping_state_id'))->cities;
    }

    function getProfileRegion() {
        if(Auth::check() == true && $this->page['states'] == null && $this->page['cities'] == null) {
            $this->page['states'] = State::all();
            $this->page['cities'] = State::find($this->user()->state_id)?State::find($this->user()->state_id)->cities:null;
        }
    }

}
