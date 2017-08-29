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
         *  - order status has never been paid
         **/
        if ( 
            ! $orderStatus or
            $invoice->related->status_code == $orderStatus->code or
            $invoice->related->isPaid()
        ) return;

        $invoice->related->updateStatus($orderStatus->code, $record->comment);
    }

}
