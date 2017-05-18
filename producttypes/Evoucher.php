<?php namespace Octommerce\Octommerce\ProductTypes;

use Octommerce\Octommerce\Classes\ProductTypeBase;

class Evoucher extends ProductTypeBase
{

	public function typeDetails()
    {
        return [
            'name'        => 'e-Voucher Product',
            'code'        => 'evoucher',
            'description' => 'e-Voucher product.',
        ];
    }

    public function invoicePaid($invoice)
    {
        $productData = $this->product->pivot->data ?: [];

        /**
         * Generate evoucher codes based on product qty
         **/
        for ($n = 1; $n <= $this->product->pivot->qty; $n++) {
            $evoucherCodes[] = (string) rand(); // TODO: Add implementation to generate evoucher
        }

        /**
         * Merge old data with new data
         **/
        $newProductData = array_merge_recursive($productData, [
            'evouchers' => [
                "{$this->product->sku}" => $evoucherCodes
            ]
        ]);

        /**
         * Update pivot data of product
         **/
        $invoice->related->products()->updateExistingPivot($this->product->id, [
            'data' => json_encode($newProductData)
        ]);
    }

}
