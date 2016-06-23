<?php namespace Octommerce\Octommerce\Models;

use Model;
use Octommerce\Octommerce\Models\City;
use Rainlab\Location\Models\State;

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
        'message',
        'subtotal',
    ];

    protected $dates = ['status_updated_at', 'expired_at', 'deleted_at'];

    /**
     * The attributes that should be appended to native types.
     *
     * @var array
     */
    protected $appends = ['total'];

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
    public $morphOne = [];
    public $morphMany = [
        'invoices' => [
            'Responsiv\Pay\Models\Invoice',
            'name' => 'related'
        ],
    ];
    public $attachOne = [];
    public $attachMany = [];

    public function getTotalAttribute()
    {
        return $this->subtotal - $this->discount;
    }

    public function getShippingCityNameAttribute()
    {
        if($this->shipping_city_id) {
            $city = City::whereId($this->shipping_city_id)->first();
            return $city->name;
        }

        return null;
    }

    public function getShippingStateNameAttribute()
    {
        if($this->shipping_state_id) {
            $state = State::whereId($this->shipping_state_id)->first();
            return $state->name;
        }

        return null;
    }

    public function updateStatus($statusCode)
    {
        if ($status = OrderStatus::find($statusCode)) {
            OrderStatusLog::createRecord($status, $this);
        }
    }

    public function beforeCreate()
    {
        $this->order_no = $this->generateOrderNo();
    }

    public function beforeSave()
    {
        $this->copyShippingAddress();
    }

    public function afterCreate()
    {
        OrderStatusLog::createRecord('waiting', $this);
    }

    public function sendEmailToCustomer()
    {
        return $this->status_logs->last()->sendEmailToCustomer();
    }

    public function sendEmailToAdmin()
    {
        $order = $this;

        Mail::send('octommerce.octommerce::mail.admin_order_template', compact('order'), function($message) {
            $message->to('sales@turez.id')->cc('helpdesk@turez.id');
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
        }
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
