<?php namespace Octommerce\Octommerce\ProductTypes;

use Octommerce\Octommerce\Classes\ProductTypeBase;

class Variable extends ProductTypeBase
{

	public function typeDetails()
    {
        return [
            'name'        => 'Variable Product',
            'code'        => 'variable',
            'description' => 'Variable product.',
        ];
    }

}