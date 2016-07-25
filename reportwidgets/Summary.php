<?php namespace Octommerce\Octommerce\ReportWidgets;

use Exception;
use Backend\Classes\ReportWidgetBase;
use Octommerce\Octommerce\Models\Order;
use Octommerce\Octommerce\Models\Product;

class Summary extends ReportWidgetBase
{
    public function render()
    {
        try {
            $this->loadData();
        }
        catch (Exception $ex) {
            $this->vars['error'] = $ex->getMessage();
        }

        return $this->makePartial('widget');
    }

    public function defineProperties()
    {
        return [
            'title' => [
                'title'             => 'backend::lang.dashboard.widget_title_label',
                'default'           => 'Summary',
                'type'              => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error'
            ],
            'days' => [
                'title'             => 'Number of days to display data for',
                'default'           => '30',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$'
            ]
        ];
    }

    public function onRefresh()
    {
        return "YAA";
    }

    protected function loadData()
    {
        $sales = Order::sales();

        $this->vars['sales_amount'] = number_format($sales->sum('subtotal')); //TODO: change to total;
        $this->vars['sales_count'] = number_format($sales->count());

        $last_sale = $sales->orderBy('created_at', 'desc')->first();

        $this->vars['sale_last_date'] = $last_sale ? $last_sale->created_at : null;

        $products = Product::query();

        $this->vars['products_count'] = number_format($products->count());
        $this->vars['products_out_of_stock'] = number_format($products->count() - $products->available()->count());
    }
}
