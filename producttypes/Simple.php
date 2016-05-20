<?php namespace Octommerce\Octommerce\ProductTypes;

use Octommerce\Octommerce\Classes\ProductTypeBase;

class Simple extends ProductTypeBase
{

	public function typeDetails()
    {
        return [
            'name'        => 'Simple Product',
            'code'        => 'simple',
            'description' => 'Just a simple product.',
        ];
    }

}