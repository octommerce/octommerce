<?php namespace Octommerce\Octommerce\Listeners;

use Octommerce\Octommerce\Models\OrderStatus;

class UpdateOrderStatus 
{

    public function filterStatus($record, $invoice, $statusId, $previousStatus)
    {
        $orderStatus = OrderStatus::whereCode($record->status->code)->first();

        /**
         * Only process if 
         *  - order_statuses table has the same status code
         *  - new status not same with existing order status
         **/
        if ( ! $orderStatus or $invoice->related->status_code == $orderStatus->code) return;

        $invoice->related->updateStatus($orderStatus->code, $record->comment);
    }

}
