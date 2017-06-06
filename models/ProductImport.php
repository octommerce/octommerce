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
            'up_sell_skus',
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
                        $fillableData[$key] = $data[$key] = null;
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

                    if ($parent) {
                        $product->parent_id = $parent->id;
                    }
                }

                // Brand
                if (isset($data['brand_name']) && $data['brand_name']) {
                    $brand = Brand::whereName($data['brand_name'])->first();

                    if ($brand) {
                        $product->brand_id = $brand->id;
                    }
                }

                // Categories
                if (isset($data['category_names']) && $data['category_names']) {

                    $categoryIds = [];
                    foreach (explode(';', $data['category_names']) as $categoryName) {

                        $category = Category::whereName($categoryName)->first();

                        if ($category) {
                            $categoryIds[] = $category->id;
                        }
                    }

                    $product->categories()->sync($categoryIds);
                }

                // Up Sells
                if (isset($data['up_sell_skus']) && $data['up_sell_skus']) {

                    $productIds = [];
                    foreach (explode(';', $data['up_sell_skus']) as $sku) {

                        $upsellProduct = Product::whereSku($sku)->first();

                        if ($upsellProduct) {
                            $productIds[] = $upsellProduct->id;
                        }
                    }

                    $product->up_sells()->sync($productIds);
                }

                // Cross Sells
                if (isset($data['cross_sell_skus']) && $data['cross_sell_skus']) {

                    $productIds = [];
                    foreach (explode(';', $data['cross_sell_skus']) as $sku) {

                        $crossSellProduct = Product::whereSku($sku)->first();

                        if ($crossSellProduct) {
                            $productIds[] = $crossSellProduct->id;
                        }
                    }

                    $product->cross_sells()->sync($productIds);
                }

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