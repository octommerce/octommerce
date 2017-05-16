<?php namespace Octommerce\Octommerce\Classes;

/**
 * This is a base class for rule
 */
class ProductTypeBase
{

    protected $product;

    public function __construct($product = null)
    {
        $this->details = $this->typeDetails();
        $this->product = $product;
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

    public function beforeCreateProduct()
    {
    }

    public function afterCreateProduct()
    {
    }

    public function beforeUpdateProduct()
    {
    }

    public function afterUpdateProduct()
    {
    }

    public function beforeSaveProduct()
    {
    }

    public function afterSaveProduct()
    {
    }

    public function beforeDeleteProduct()
    {
    }

    public function afterDeleteProduct()
    {
    }

    public function __toString()
    {
        return $this->details['code'];
    }

}
