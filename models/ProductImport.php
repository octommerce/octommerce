<?php namespace Octommerce\Octommerce\Models;

use Backend\Models\ImportModel;

/**
 * ProductImport Model
 */
class ProductImport extends ImportModel
{
    /**
     * @var array The rules to be applied to the data.
     */
    public $rules = [];

    public function importData($results, $sessionKey = null)
    {
        $extraColumns = [
            'parent_sku',
            'brand_name',
            'category_names',
            'up_sells_skus',
            'cross_sell_skus',
        ];

        foreach ($results as $row => $data) {

            try {

                if (isset($data['id']) && $data['id']) {
                    $product = Product::find($data['id']);

                    if (!$product) {
                        $product = new Product;
                    }

                } elseif (isset($data['sku']) && $data['sku']) {
                    $product = Product::firstOrNew(['sku' => $data['sku']]);
                } else {
                    $product = new Product;
                }

                // If has product id, it's updating
                $isUpdate = $product->id ? true : false;

                // Prepare fillable data
                $fillableData = $data;

                // Filter every columns
                foreach($fillableData as $key => $value) {

                    // If empty, set to null
                    if ($value == '') {
                        $fillableData[$key] = null;
                    }

                    // If it's extra column, unset it
                    if (in_array($key, $extraColumns)) {
                        unset($fillableData[$key]);
                    }
                }

                // Fill columns
                $product->fill($fillableData);

                // Parent product
                if (isset($data['parent_sku']) && $data['parent_sku']) {
                    $parent = Product::whereSku($data['parent_sku'])->first();
                    $product->parent_id = $parent->id;
                }

                // Brand
                if (isset($data['brand_name']) && $data['brand_name']) {
                    $brand = Brand::whereName($data['brand_name'])->first();
                    $product->brand_id = $brand->id;
                }

                // TODO:
                // - Categories
                // - Linked products

                $product->save();

                if ($isUpdate) {
                    $this->logUpdated();
                } else {
                    $this->logCreated();
                }
            }
            catch (\Exception $ex) {
                $this->logError($row, $ex->getMessage());
            }

        }
    }
}