<?php namespace Octommerce\Octommerce\Listeners;

use Responsiv\Pay\Models\InvoiceStatus;

class UpdateInvoiceStatus 
{

    public function filterStatus($order, $statusCode)
    {
        $invoiceStatus = InvoiceStatus::whereCode($statusCode)->first();

        /**
         * Only process if 
         *  - invoice_statuses table has the same status code
         *  - new status not same with existing invoice status
         **/
        if ( ! $invoiceStatus or $order->invoice->status_code == $invoiceStatus->code) return;

        $order->invoice->updateInvoiceStatus($invoiceStatus->code);
    }

}
