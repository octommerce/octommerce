<?php namespace Octommerce\Octommerce\Components;

use Cms\Classes\ComponentBase;

class Order extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'order Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

}