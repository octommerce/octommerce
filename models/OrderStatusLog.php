<?php namespace Octommerce\Octommerce\Models;

use Model;
use Mail;
use ApplicationException;
use System\Models\MailTemplate;
use Octommerce\Octommerce\Models\Order;
use Octommerce\Octommerce\Models\OrderStatus;

/**
 * OrderStatusLog Model
 */
class OrderStatusLog extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_order_status_logs';

    public $timestamps = false;

    protected $dates = ['timestamp'];

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['order_id', 'status_code'];

    protected $jsonable = ['data'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'order' => 'Octommerce\Octommerce\Models\Order',
        'status' => 'Octommerce\Octommerce\Models\OrderStatus',
        'admin' => 'Backend\Models\User',
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function afterSave()
    {
        $this->checkOrderStatus(); 
    }

    /**
     * Check order status to decide to send an email or not
     *
     * @return void
     */
    public function checkOrderStatus()
    {
        $orderStatus = OrderStatus::find($this->status_code);

        if (! $orderStatus) {
            throw new ApplicationException('Order status not found!');
        }

        if ($orderStatus->is_active && $orderStatus->send_email) {
            $order = Order::find($this->order_id);

            if (! $order) {
                throw new ApplicationException('Order not found!');
            }

            $this->sendEmailToCustomer($orderStatus, $order);
        }
    }

    /**
     * Send an email to customer 
     * @param $orderStatus
     * @param $order
     *
     * @return void
     */
    public function sendEmailToCustomer($orderStatus, $order)
    {
        $mailTemplate = MailTemplate::find($orderStatus->mail_template_id);

        if (! $mailTemplate) {
            throw new ApplicationException('Mail template not found!'); 
        }

        Mail::send($mailTemplate->code, compact('order'), function($message) use ($order, $orderStatus) {
            $message->to($order->email, $order->name);

            if($orderStatus->attach_pdf) {
            //     $message->attach($order->pdf->getLocalPath(), ['as' => 'order-' . $order->invoice_no . '.pdf']);
            }
        });
    }

}
