<?php namespace Octommerce\Octommerce\Models;

use Db;
use Mail;
use Queue;
use Event;
use Model;
use Exception;
use BackendAuth;
use Carbon\Carbon;
use ApplicationException;
use System\Models\MailTemplate;
use Octommerce\Octommerce\Models\Order;
use Octommerce\Octommerce\Models\OrderStatus;
use Octommerce\Octommerce\Models\Settings;

/**
 * OrderStatusLog Model
 */
class OrderStatusLog extends Model
{
    protected $previousStatus;

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
        'status' => [
            'Octommerce\Octommerce\Models\OrderStatus',
            'key' => 'status_code',
        ],
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

    public function setPreviousStatus($previousStatus)
    {
        $this->previousStatus = $previousStatus;
    }

    public function getStatusOptions()
    {
        if ($this->previousStatus) {
            return OrderStatus::find($this->previousStatus)->children()->lists('name', 'code');
        }

        return OrderStatus::lists('name', 'code');
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

            if ($orderStatus->send_email_to_admin) {
                $this->sendEmailToAdmin($orderStatus, $order);
            }
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
            return;
        }

        // Get newest status log
        $statusLog = $this->where('order_id', '=', $order->id)
            ->orderBy('timestamp', 'DESC')->first();

        $orderId = $order->id;
        $orderStatusCode = $orderStatus->code;
        $statusLogId = $statusLog->id;

        Queue::later(60, function($job) use ($orderId, $orderStatusCode, $statusLogId) {
            $order = Order::find($orderId);
            $statusLog = OrderStatusLog::find($statusLogId);
            $orderStatus = OrderStatus::find($orderStatusCode);

            Mail::send($orderStatus->mail_template->code, compact('order', 'statusLog'), function($message) use ($order, $orderStatus) {
                $message->to($order->email, $order->name);

                if($orderStatus->attach_pdf) {
                    $message->attach($order->pdf->getLocalPath(), ['as' => 'order-' . $order->order_no . '.pdf']); 
                }
            });

            $job->delete();
        });

    }

    /**
     * Send an email to admin
     * @param $orderStatus
     * @param $order
     *
     * @return void
     */
    public function sendEmailToAdmin()
    {
        $order = $this->order;
        $orderStatus = $order->status;

        if (! $orderStatus->admin_mail_template) {
            return;
        }

        $orderId = $order->id;
        $orderStatusCode = $orderStatus->code;

        Queue::later(60, function($job) use ($orderId, $orderStatusCode) {
            $order = Order::find($orderId);
            $orderStatus = OrderStatus::find($orderStatusCode);

            Mail::send($orderStatus->admin_mail_template->code, compact('order'), function($message) use ($order, $orderStatus) {
                $message->to(Settings::get('admin_email'), 'Admin');

                if($orderStatus->attach_pdf) {
                    $message->attach($order->pdf->getLocalPath(), ['as' => 'order-' . $order->order_no . '.pdf']);
                }
            });

            $job->delete();
        });
    }

    public static function createRecord($statusCode, $order, $note = null, $data = null)
    {
        if (! $order instanceof \Octommerce\Octommerce\Models\Order)
            return;

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
            $record->data = array_except($data, ['status', 'note']);
            $record->timestamp = Carbon::now();
            $record->note = $note;

            /*
             * Extensibility
             */
            if (Event::fire('octommerce.octommerce.beforeUpdateOrderStatus', [$record, $order, $statusCode, $previousStatus], true) === false)
                return false;

            if ($record->fireEvent('octommerce.beforeUpdateOrderStatus', [$record, $order, $statusCode, $previousStatus], true) === false)
                return false;

            /*
             * Update order status
             */
            $order->status_code = $statusCode;
            $order->status_updated_at = Carbon::now();
            $order->save();

            $record->save();

            $order->onChangeStatus($statusCode, $previousStatus);

            Db::commit();
        }
        catch (Exception $e) {
            Db::rollBack();

            throw $e;
        }

    }
}
