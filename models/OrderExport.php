<?php namespace Octommerce\Octommerce\Models;

use Backend\Models\ExportModel;

/**
 * OrderExport Model
 */
class OrderExport extends ExportModel
{
    protected $fillable = ['start_date', 'end_date', 'status', 'is_expand_product'];

    public function exportData($columns, $sessionKey = null)
    {
        $query = Order::with('city', 'state', 'shipping_city', 'shipping_state', 'invoice', 'status');

        //
        // Filter
        //
        if ($this->start_date) {
            $query->whereDate('created_at', '>=', $this->start_date);
        }

        if ($this->end_date) {
            $query->whereDate('created_at', '<=', $this->end_date);
        }

        if ($this->status) {
            $query->whereStatusCode($this->status);
        }

        $orders = $query->get();

        if ($this->is_expand_product) {

            $orders->each(function($order) use ($columns){
                foreach($order->products as $product) {
                    $order->addVisible($columns);

                    //
                    // Parsing Cities
                    //
                    $order->city_name           = $order->city ? $order->city->name : '';
                    $order->state_name          = $order->state ? $order->state->name : '';
                    $order->shipping_city_name  = $order->shipping_city ? $order->shipping_city->name : '';
                    $order->shipping_state_name = $order->shipping_state ? $order->shipping_state->name : '';

                    //
                    // Parsing products
                    //
                    $order->product_skus  = $product->sku;
                    $order->product_names = $product->pivot->name;
                    $order->product_qty   = $product->pivot->qty;
                    $order->product_price = $product->pivot->price;

                    //
                    // Parsing invoice
                    //
                    $invoice = $order->invoice;

                    if ($order->invoice) {
                        $order->payment_method = $invoice->payment_method ? $invoice->payment_method->name : '';
                        $order->unique_code    = $invoice->unique_number > 0 ? $invoice->unique_number : '';
                        $order->due_at         = $invoice->due_at ? $invoice->due_at : '';
                    }

                    $order->status_name = $order->status ? $order->status->name : '';
                }
            });

        } else { // If not expand product, (new style report template)

            $orders->each(function($order) use ($columns) {
                $order->addVisible($columns);

                //
                // Parsing Cities
                //
                $order->city_name           = $order->city ? $order->city->name : '';
                $order->state_name          = $order->state ? $order->state->name : '';
                $order->shipping_city_name  = $order->shipping_city ? $order->shipping_city->name : '';
                $order->shipping_state_name = $order->shipping_state ? $order->shipping_state->name : '';

                //
                // Parsing products
                //
                $productSkusArray = [];
                $productNamesArray = [];

                foreach($order->products as $product) {
                    $productSkusArray[] = $product->pivot->qty . ' x ' . $product->sku;
                    $productNamesArray[] = $product->pivot->qty . ' x ' . $product->pivot->name . ' @ ' . $product->pivot->price;
                }

                $order->product_skus  = implode(';', $productSkusArray);
                $order->product_names = implode(';', $productNamesArray);

                //
                // Parsing invoice
                //
                $invoice = $order->invoice;

                if ($order->invoice) {
                    $order->payment_method = $invoice->payment_method ? $invoice->payment_method->name : '';
                    $order->unique_code    = $invoice->unique_number > 0 ? $invoice->unique_number : '';
                    $order->due_at         = $invoice->due_at ? $invoice->due_at : '';
                }

                $order->status_name = $order->status ? $order->status->name : '';
            });

        }

        return $orders;
    }
}
