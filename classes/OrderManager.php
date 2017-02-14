<?php namespace Octommerce\Octommerce\Classes;

use Db;
use Auth;
use Mail;
use Event;
use Carbon\Carbon;
use RainLab\User\Models\User;
use Octommerce\Octommerce\Models\Order;
use Octommerce\Octommerce\Models\Cart;
use Octommerce\Octommerce\Models\City;
use Responsiv\Pay\Models\Invoice;
use Responsiv\Pay\Models\InvoiceItem;

class OrderManager
{
	use \October\Rain\Support\Traits\Singleton;
    use \October\Rain\Support\Traits\Emitter;

    public function create($data)
    {
        try {

            Db::beginTransaction();

            $cart = \Cart::get();

            if (!$cart) {
                throw new Exception('You have no item in cart.');
            }

            $user = $this->getOrRegisterUser($data);

            $order = new Order($data);

            $order->fill([
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'subtotal' => $cart->total_price,
            ]);

            if (isset($data['city_id']) && $cityId = $data['city_id']) {
                $city = City::find($cityId);

                if ($city) {
                    $order->city()->add($city);
                }

                if ($city->state) {
                    $order->state()->add($city->state);
                }
            }

            $order->user()->add($user);

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

            /*
             * Extensibility
             */
            $this->fireEvent('order.afterCreate', [$order, $data]);
            Event::fire('order.afterCreate', [$order, $data]);

            Db::commit();

            $order = Order::find($order->id);

            $invoice = Invoice::create([
                'user_id'      => $user->id,
                'first_name'   => $order->name,
                'email'        => $order->email,
                'phone'        => $order->phone,
                'company'      => $order->company,
                'street_addr'  => $order->address,
                'city'         => $order->city ? $order->city->name : null,
                'zip'          => $order->postcode,
                'state_id'     => $order->state ? $order->state->id : null,
                'country_id'   => $order->state ? $order->state->country->id : null,
                'due_at'       => $order->expired_at,
            ]);

            foreach($cart->products as $product) {
                $invoiceItem = new InvoiceItem([
                    'description' => $product->name,
                    'quantity' => $product->pivot->qty,
                    'price' => $product->pivot->price,
                    'discount' => $product->pivot->discount,
                ]);

                $invoice->items()->save($invoiceItem);
            }

            // If have a discount, put to invoice items
            if ($order->discount) {
                $discountItem = new InvoiceItem([
                    'description' => 'Discount',
                    'quantity' => 1,
                    'price' => 0,
                    'discount' => $order->discount,
                ]);

                $invoice->items()->save($discountItem);
            }

            $order->invoices()->add($invoice);

            /*
             * Extensibility
             */
            $this->fireEvent('order.afterAddInvoice', [$order, $invoice]);
            Event::fire('order.afterAddInvoice', [$order, $invoice]);

            $invoice->save();

            $order->save();

            \Cart::clear();

            return $order;
        }
        catch (Exception $e) {
            Db::rollBack();

            throw $e;
        }
    }

    protected function getOrRegisterUser($data, $update = true)
    {
        if (! Auth::check()) {

            if (User::whereEmail($data['email'])->count()) {
                throw new \ApplicationException('This email is already exist. Please login first.');
            }

            $data['password_confirmation'] = $data['password'] = $this->generateUserPassword();

            // Register, no need activation
            $user = Auth::register($data, true);

            // Logged in directly
            Auth::login($user);

            $this->sendPasswordUser($user, $data['password']);

        } else {
            $user = Auth::getUser();

            // Update data phone if any
            if($update) {
                $user->fill($data);
                $user->save();
            }
        }

        return $user;
    }

    public function checkExpiredOrders()
    {
        Order::whereStatusCode('waiting')
            ->where('expired_at', '<=', Carbon::now())
            ->get()
            ->each(function($order) {

                // Set the order status to expired
                $order->updateStatus('expired');

                // TODO:
                // Extensibility
            });
    }

    public function remindWaitingPayments()
    {
        $hours = 24; // A day before expired

        Order::whereStatusCode('waiting')
            ->where('expired_at', '>=', Carbon::now()->addHours($hours))
            ->where('expired_at', '<', Carbon::now()->addHours($hours + 1))
            ->get()
            ->each(function($order) {
                // Set payment reminder
                $order->sendPaymentReminder();
            });
    }

    public function remindAbandonedCarts()
    {
        $hours = 3 * 24; // After 3 days no update

        Cart::has('user')
            ->has('products')
            ->where('updated_at', '>=', Carbon::now()->subHours($hours))
            ->where('updated_at', '<', Carbon::now()->subHours($hours - 1))
            ->get()
            ->each(function($cart) {
                // Send reminder
                $cart->sendReminder();
            });
    }

    protected function generateUserPassword($length = 8)
    {
        $characters = '123456789abcdefghijklmnpqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    protected function sendPasswordUser($user, $password) {
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
        ];

        Mail::send('octommerce.octommerce::mail.password_user', $data, function ($message) use ($user) {
            $message->to($user->email, $user->name);
        });
    }
}
