<?php namespace Octommerce\Octommerce\Models;

use Model;

/**
 * Order Model
 */
class Order extends Model
{
    use \October\Rain\Database\Traits\SoftDeleting;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_orders';

    // protected $dates = ['deleted_at'];

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    protected $dates = ['expired_at', 'deleted_at'];

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
        'user' => 'RainLab\User\Models\User'
    ];
    public $belongsToMany = [
        'products' => [
            'Octommerce\Octommerce\Models\Product',
            'table' => 'octommerce_octommerce_order_product',
        ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function sendEmailToUser()
    {
        $order = $this;

        Mail::send('octommerce.octommerce::mail.user_order_template', compact('order'), function($message) use ($order) {
            $message->to($order->email, $order->name);

            // if($order->pdf) {
            //     $message->attach($order->pdf->getLocalPath(), ['as' => 'order-' . $order->invoice_no . '.pdf']);
            // }
        });
    }

    public function sendEmailToAdmin()
    {
        $order = $this;

        Mail::send('octommerce.octommerce::mail.admin_order_template', compact('order'), function($message) {
            $message->to('sales@turez.id')->cc('helpdesk@turez.id');
        });
    }
}
