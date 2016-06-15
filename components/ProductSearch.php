<?php namespace Octommerce\Octommerce\Components;

use Input;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Product;
use Octommerce\Octommerce\Models\Category;
use Octommerce\Octommerce\Models\Brand;

class ProductSearch extends ComponentBase
{

    public $query;
    public $products;
    public $total;

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

        $this->products = $products = Product::search($query)->orderBy('relevance', 'desc')->limit(12)->get();

        $this->total = $products->count();

        // $this->products = $products->paginate(12);
    }

}