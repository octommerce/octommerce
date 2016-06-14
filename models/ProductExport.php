<?php namespace Octommerce\Octommerce\Models;

use Backend\Models\ExportModel;

/**
 * ProductExport Model
 */
class ProductExport extends ExportModel
{

    protected $fillable = ['start_date', 'end_date', 'status'];

    public function exportData($columns, $sessionKey = null)
    {
    	$query = Product::query();

    	// TODO: Filter

        $products = $query->get();

        $products->each(function($product) use ($columns) {
        	$product->brand_name = $product->brand ? $product->brand->name : null;
        	$product->parent_sku = $product->parent ? $product->parent->sku : null;

        	$product->category_names = $product->categories->count() ? implode(';', $product->categories->pluck('name')->toArray()) : null;

        	$product->up_sell_skus = $product->up_sells->count() ? implode(';', $product->up_sells->pluck('sku')->toArray()) : null;

        	$product->cross_sell_skus = $product->cross_sells->count() ? implode(';', $product->cross_sells->pluck('sku')->toArray()) : null;
        });

        return $products->toArray();
    }
}