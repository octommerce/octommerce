<?php namespace Octommerce\Octommerce\Models;

use Carbon\Carbon;
use Backend\Models\ExportModel;

/**
 * OrderExport Model
 */
class OrderExport extends ExportModel
{
    private $tempOrder;

    protected $fillable = ['start_date', 'end_date', 'status', 'is_expand_product'];

    public function exportData($columns, $sessionKey = null)
    {

        if ($this->is_expand_product) {

            $query = OrderDetail::with('product', 'order')->has('order');

            //
            // Filter
            //
            if ($this->start_date) {
                $query->whereHas('order', function($query) {
                    $query->whereDate('created_at', '>=', Carbon::parse($this->start_date)->format('Y-m-d'));
                });
            }

            if ($this->end_date) {
                $query->whereHas('order', function($query) {
                    $query->whereDate('created_at', '<', Carbon::parse($this->end_date)->addDay()->format('Y-m-d'));
                });
            }

            if ($this->status) {
                $query->whereHas('order', function($query) {
                    $query->whereStatusCode($this->status);
                });
            }

            $orders = $query->get();

            $orders->each(function($order, $key) use ($columns) {

                /**if order_id from previous temporary order has same order_id from current order_id
                 @and temporary order has not empty
                **/
                if($this->tempOrder != null && $this->tempOrder->order_id == $order->order_id) {
                   //Calculate current order subtotal and add up with previous subtotal of temporary order
                   $subtotal = $order->product ? ($order->price * $order->qty) + $this->tempOrder->subtotal : '';
                } else {
                    //otherwise(current order_id is not same with previous order_id)
                    $this->tempOrder = null;
                }

                if(null === $this->tempOrder) {
                    //put current order into temporary order
                    $this->tempOrder = $order;
                    //put current subtotal calculation
                    $subtotal = $order->product ? $order->price * $order->qty : '';
                }

                $order->addVisible($columns);

                $order->order_no = $order->order ? $order->order->order_no : '';
                $order->name     = $order->order ? $order->order->name : '';
                $order->email    = $order->order ? $order->order->email : '';
                $order->phone    = $order->order ? $order->order->phone : '';
                $order->postal_code = $order->order ? $order->order->postcode : '';
                $order->address = $order->order ? $order->order->address : '';

                $order->shipping_name = $order->order ? $order->order->shipping_name : '';
                $order->shipping_phone = $order->order ? $order->order->shipping_phone : '';
                $order->shipping_address = $order->order ? $order->order->shipping_address : '';
                $order->shipping_postal_code = $order->order ? $order->order->shipping_postcode: '';

                $order->state_name = $order->order->state ? $order->order->state->name : '';
                $order->city_name = $order->order->city ? $order->order->city->name : '';
                $order->shipping_city_name  = $order->order->shipping_city ? $order->order->shipping_city->name : '';
                $order->shipping_state_name = $order->order->shipping_state ? $order->order->shipping_state->name : '';
                $order->shipping_cost = $order->order ? $order->order->shipping_cost : '';


                $order->product_names  = $order->product ? $order->product->name : '';
                $order->product_skus  = $order->product ? $order->product->sku : '';
                $order->product_price = $order->price ? $order->price : '';
                $order->product_discount = $order->discount ? $order->discount : '';
                $order->product_qty = $order->qty ? $order->qty : '';
                $order->subtotal = $subtotal;//$order->product ? $order->price * $order->qty : '';
                $order->misc_fee = $order->order ? $order->order->misc_fee : '';
                $order->tax = $order->order ? $order->order->tax : '';
                $order->message = $order->order ? $order->order->message : '';
                $order->notes = $order->order ? $order->order->notes : '';
                $order->created_at = $order->order ? $order->order->created_at : '';

                $order->discount = $order->order ? $order->order->discount : '';
                $order->total = $order->order ? $order->order->total : '';


                if ($invoice = $order->order->invoice) {
                    $order->payment_method = $invoice->payment_method ? $invoice->payment_method->name : '';
                    $order->unique_code    = $invoice->unique_number > 0 ? $invoice->unique_number : '';
                    $order->due_at         = $invoice->due_at ? $invoice->due_at : '';
                }

                $order->status_name = $order->order->status ? $order->order->status->name : '';


                //Replace previous temporary order with current order for next evaluation
                $this->tempOrder = $order;

            });

        } else { // If not expand product, (new style report template)

            $query = Order::with('city', 'state', 'shipping_city', 'shipping_state', 'invoice', 'status');

            if ($this->start_date) {
                $query->whereDate('created_at', '>=', Carbon::parse($this->start_date)->format('Y-m-d'));
            }

            if ($this->end_date) {
                $query->whereDate('created_at', '<', Carbon::parse($this->end_date)->addDay()->format('Y-m-d'));
            }

            if($this->status) {
                $query->whereStatusCode($this->status);
            }

            $orders = $query->get();

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
