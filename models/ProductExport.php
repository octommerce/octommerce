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

        return $products->toArray();
    }
}