<?php namespace Octommerce\Octommerce\Classes;

/**
 * This is a base class for rule
 */
class ProductTypeBase
{

    public function __construct()
    {
        $this->details = $this->typeDetails();
    }

    public function typeDetails()
    {
    }

    public function registerFields()
    {
    }

    public function beforeAddToCart($cart, $qty)
    {
    }

    public function afterAddToCart($cart, $qty)
    {
    }

    public function beforeCreateProduct($product)
    {
    }

    public function afterCreateProduct($product)
    {
    }

    public function beforeSaveProduct($product)
    {
    }

    public function afterSaveProduct($product)
    {
    }

    public function beforeDeleteProduct($product)
    {
    }

    public function afterDeleteProduct($product)
    {
    }

}