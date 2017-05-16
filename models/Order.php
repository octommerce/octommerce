<?php namespace Octommerce\Octommerce\Models;

use Mail;
use Event;
use Model;
use Carbon\Carbon;
use Octommerce\Octommerce\Models\City;
use Rainlab\Location\Models\State;
use Responsiv\Pay\Models\Invoice;

/**
 * Order Model
 */
class Order extends Model
{
    use \October\Rain\Database\Traits\SoftDeleting;
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_orders';

    /**
     * Validation rules
     */
    public $rules = [
        'name' => 'required|between:6,255',
        'email' => 'required|between:6,255|email',
        'phone' => 'required',
    ];

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'postcode',
        'city_id',
        'state_id',
        'address',
        'company',
        'is_same_address',
        'shipping_name',
        'shipping_phone',
        'shipping_company',
        'shipping_address',
        'shipping_city_id',
        'shipping_state_id',
        'shipping_postcode',
        'shipping_cost',
        'message',
        'subtotal',
        'total_weight',
    ];

    protected $dates = ['status_updated_at', 'expired_at', 'deleted_at'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'status_logs' => 'Octommerce\Octommerce\Models\OrderStatusLog',
    ];
    public $belongsTo = [
        'user' => 'RainLab\User\Models\User',
        'status' => [
            'Octommerce\Octommerce\Models\OrderStatus',
            'key' => 'status_code',
        ],
        'city' => 'Octommerce\Octommerce\Models\City',
        'state' => 'RainLab\Location\Models\State',
        'shipping_city' => 'Octommerce\Octommerce\Models\City',
        'shipping_state' => 'RainLab\Location\Models\State',
    ];
    public $belongsToMany = [
        'products' => [
            'Octommerce\Octommerce\Models\Product',
            'table' => 'octommerce_octommerce_order_product',
            'pivot' => ['qty', 'price', 'discount', 'name'],
        ],
    ];
    public $morphTo = [];
    public $morphOne = [
        'invoice' => [
            'Responsiv\Pay\Models\Invoice',
            'name' => 'related',
        ],
    ];
    public $morphMany = [
        'invoices' => [
            'Responsiv\Pay\Models\Invoice',
            'name' => 'related'
        ],
    ];
    public $attachOne = [];
    public $attachMany = [];

    public function scopeSales($query)
    {
        return $query->where(function($query) {
            $query->whereStatusCode('paid')
                ->orWhere('status_code', 'shipped')
                ->orWhere('status_code', 'packing')
                ->orWhere('status_code', 'delivered');
        });
    }

    public function updateStatus($statusCode, $note = '', $data = [])
    {
        if ($status = OrderStatus::find($statusCode)) {
            OrderStatusLog::createRecord($status, $this, $note, $data);
        }

        /*
         * Extensibility
         */
        Event::fire('order.afterUpdateStatus', [$this, $statusCode]);
    }

    public function beforeCreate()
    {
        $this->order_no = $this->generateOrderNo();

        $now = Carbon::parse($this->created_at);

        $this->expired_at = Carbon::now()
            ->addWeekdays(2)
            ->addHours($now->format('H'))
            ->addMinutes($now->format('i'))
            ->addSeconds($now->format('s'));

        // TODO: Check holidays
    }

    public function beforeSave()
    {
        $this->copyShippingAddress();
        $this->calculateTotal();
    }

    public function afterCreate()
    {
        OrderStatusLog::createRecord('waiting', $this);
    }

    public function afterDelete()
    {
        $this->invoices->first()->delete();
    }

    public function sendEmailToCustomer()
    {
        return $this->status_logs->last()->sendEmailToCustomer();
    }

    public function sendPaymentReminder()
    {
        $order = $this;

        Mail::send('octommerce.octommerce::mail.payment_reminder', compact('order'), function($message) use($order) {
            $message->to($order->email);
        });
    }

    public function scopeFilterPaymentMethods($query, $paymentMethods)
    {
        return $query->whereHas('invoices', function($q) use ($paymentMethods) {

            $q->whereIn('payment_method_id', $paymentMethods);
        });
    }

    public function onChangeStatus($statusCode, $previousStatusCode = null)
    {
        if ($statusCode == 'paid') {

            foreach($this->products as $product) {
                $product->holdStock($product->pivot->qty);
            }
        }

        if ($statusCode == 'void' || $statusCode == 'closed') {

            foreach($this->products as $product) {
                $product->releaseStock($product->pivot->qty);
            }
        }
    }

    protected function copyShippingAddress()
    {
        if ($this->is_same_address) {
            $this->fill([
                'shipping_name'     => $this->name,
                'shipping_phone'    => $this->phone,
                'shipping_company'  => $this->company,
                'shipping_address'  => $this->address,
                'shipping_city_id'  => $this->city_id,
                'shipping_state_id' => $this->state_id,
                'shipping_postcode' => $this->postcode,
            ]);
        } else {
            $this->rules['shipping_name']     = 'required';
            $this->rules['shipping_phone']    = 'required';
            $this->rules['shipping_company']  = '';
            $this->rules['shipping_address']  = 'required';
            $this->rules['shipping_city_id']  = 'required';
            $this->rules['shipping_state_id'] = 'required';
        }
    }

    /**
     * Calculate total
     */
    public function calculateTotal()
    {
        $this->total = $this->subtotal + $this->tax + $this->shipping_cost + $this->misc_fee - $this->discount;
    }

    /**
     *
     */
    protected function generateOrderNo($length = 9, $prefix = '')
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ123456789';
        $string = $prefix;

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Check on database
        if(self::whereOrderNo($string)->count()) {
            // Recursively create again
            $string = $this->generateOrderNo($length, $prefix);
        }

        return $string;
    }
}
