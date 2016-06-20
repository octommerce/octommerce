<?php namespace Octommerce\Octommerce\Models;

use Db;
use Mail;
use Model;
use Exception;
use BackendAuth;
use Carbon\Carbon;
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
    public function sendEmailToCustomer()
    {
        $order = $this->order;
        $orderStatus = $order->status;

        if (! $orderStatus->mail_template) {
            throw new ApplicationException('Mail template not found!');
        }

        Mail::send($orderStatus->mail_template->code, compact('order'), function($message) use ($order, $orderStatus) {
            $message->to($order->email, $order->name);

            if($orderStatus->attach_pdf) {
            //     $message->attach($order->pdf->getLocalPath(), ['as' => 'order-' . $order->invoice_no . '.pdf']);
            }
        });
    }

    public static function createRecord($statusCode, $order, $note = null)
    {
        if ($statusCode instanceof Model)
            $statusCode = $statusCode->getKey();

        if ($order->status_code == $statusCode)
            return false;

        $previousStatus = $order->status_code;

        try {

            Db::beginTransaction();

            /*
             * Create record
             */
            $record = new static;
            $record->status_code = $statusCode;
            $record->order_id = $order->id;
            // $record->admin_id = BackendAuth::getUser()->id;
            $record->data = null;
            $record->timestamp = Carbon::now();
            $record->note = $note;

            /*
             * Update order status
             */
            $order->status_code = $statusCode;
            $order->status_updated_at = Carbon::now();
            $order->save();

            $record->save();

            Db::commit();
        }
        catch (Exception $e) {
            Db::rollBack();

            throw $e;
        }

    }
}
