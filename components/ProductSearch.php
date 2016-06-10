<?php namespace Octommerce\Octommerce\Components;

use Input;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Product;
use Octommerce\Octommerce\Models\Category;
use Octommerce\Octommerce\Models\Brand;

class ProductSearch extends ComponentBase
{

    public $query;
    public $result;

    public function componentDetails()
    {
        return [
            'name'        => 'Product Search Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $query = $this->query = Input::get('q');
    }

}