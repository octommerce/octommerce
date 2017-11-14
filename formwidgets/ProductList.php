<?php namespace Octommerce\Octommerce\FormWidgets;

use ApplicationException;
use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;
use Octommerce\Octommerce\Models\Product;

class ProductList extends FormWidgetBase
{
    /**
     * @var string A unique alias to identify this widget.
     */
    protected $defaultAlias = 'oc_productlist';

    public function render() {
        $this->vars['id'] = $this->getId();
        $this->vars['name'] = $this->getFieldName();
        $this->vars['model'] = $this->model;

        return $this->makePartial('product_list'); 
    }

    public function getSaveValue($value)
    {
        $newTotal = $this->getNewTotalPrice($value);

        if ($newTotal > $this->model->total) {
            throw new ApplicationException('The new total must less than current total price.');
        }

        $this->updateQtyProducts($value, $newTotal);

        return FormField::NO_SAVE_DATA;
    }

    protected function updateQtyProducts($values, $newTotal)
    {
        foreach($values as $productId => $qty) {
            $this->model->products()->updateExistingPivot($productId, [
                'qty'        => $qty,
                'qty_before' => $this->getQtyBefore($productId)
            ]);
        }

        //Update order total
        $this->model->subtotal = $newTotal;

        //Delete all items on invoice
        $this->model->invoice->items()->delete();
    }

    protected function getNewTotalPrice($values)
    {
        $productIds = $this->getProductIds($values);

        return Product::whereIn('id', $productIds)
            ->get()
            ->reduce(function($carry, $product) use ($values) {
                return $carry + $product->final_price * array_get($values, $product->id);
            });
    }

    protected function getProductIds($values)
    {
        foreach ($values as $id => $value) {
            if ($value < 0) throw new ApplicationException('Quantity number can\'t be negative');
        }

        return array_keys($values);
    }

    private function getQtyBefore($id)
    {
        return $this->model->products->filter(function($product) use ($id) {
            return $product->id == $id;
        })
            ->first()
            ->pivot
            ->qty;
    }
}
