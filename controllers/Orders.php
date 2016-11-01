<?php namespace Octommerce\Octommerce\Controllers;

use Flash;
use Backend;
use Exception;
use BackendMenu;
use Backend\Classes\Controller;
use Octommerce\Octommerce\Models\Order;
use Octommerce\Octommerce\Models\OrderStatus;
use Octommerce\Octommerce\Models\OrderStatusLog;

/**
 * Orders Back-end Controller
 */
class Orders extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController',
        'Backend.Behaviors.ImportExportController',
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';
    public $importExportConfig = 'config_import_export.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Octommerce.Octommerce', 'commerce', 'orders');
    }

    public function index()
    {
        $this->vars['orderStatuses'] = OrderStatus::with(['orders' => function($query) {
                $query->whereRaw('DATEDIFF(CURDATE(), DATE(created_at)) <= 30');
            }])
            ->whereHas('orders', function($query) {
                $query->whereRaw('DATEDIFF(CURDATE(), DATE(created_at)) <= 30');
            })
            ->get();

        $this->vars['ordersToday'] = Order::whereRaw('DATE(created_at) = CURDATE()')->count();

        return $this->asExtension('ListController')->index();
    }

    public function preview($recordId = null)
    {
        $this->vars['id'] = $recordId;
        $this->vars['order'] = Order::find($recordId);

        return $this->asExtension('FormController')->preview($recordId);
    }

    public function preview_onLoadChangeStatusForm($recordId = null)
    {
        try {
            $order = $this->formFindModelObject($recordId);
            $this->vars['currentStatus'] = $order->status->name;
            $this->vars['widget'] = $this->makeStatusFormWidget($order->status->code);
        }
        catch (Exception $ex) {
            $this->handleError($ex);
        }

        return $this->makePartial('change_status_form');
    }

    public function preview_onChangeStatus($recordId = null)
    {
        $order = $this->formFindModelObject($recordId);
        $widget = $this->makeStatusFormWidget($order->status->code);
        $data = $widget->getSaveData();

        $order->updateStatus($data['status'], $data['note']);

        Flash::success('Order status updated successfully');
        return Backend::redirect(sprintf('octommerce/octommerce/orders/preview/%s', $order->id));
    }

    public function preview_onSendEmailToCustomer($recordId = null)
    {
        $order = $this->formFindModelObject($recordId);

        $order->sendEmailToCustomer();

        Flash::success('Email sent.');
    }

    public function preview_onRegenerate($recordId = null)
    {
        $order = $this->formFindModelObject($recordId);

        $order->save();

        Flash::success('Successfully regenerated PDF.');
        return Backend::redirect(sprintf('octommerce/octommerce/orders/preview/%s', $order->id));
    }

    protected function makeStatusFormWidget($orderStatusCode)
    {
        $config = $this->makeConfig('~/plugins/octommerce/octommerce/models/orderstatuslog/fields.yaml');
        $config->model = new OrderStatusLog;
        $config->model->setPreviousStatus($orderStatusCode);
        $config->arrayName = 'OrderStatusLog';
        $config->alias = 'statusLog';
        return $this->makeWidget('Backend\Widgets\Form', $config);
    }

    public function onDelete($id)
    {
        $order = Order::find($id);

        $order->delete();

        Flash::success('That order has been deleted');

        return Backend::redirect('octommerce/octommerce/orders');
    }

    public function listExtendQuery($query)
    {
        $query->with('status', 'invoices');
    }

}
