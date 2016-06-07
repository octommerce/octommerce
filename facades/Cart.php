<?php namespace Octommerce\Octommerce\Facades;

use October\Rain\Support\Facade;

class Cart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'octommerce.octommerce.helper';
    }

    protected static function getFacadeInstance()
    {
        return new \Octommerce\Octommerce\Helpers\Cart;
    }
}
