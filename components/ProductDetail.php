<?php namespace Octommerce\Octommerce\Components;

use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Product as ProductModel;

class ProductDetail extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'ProductDetail Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'octommerce.octommerce::lang.component.product_detail.param.id_param_title',
                'description' => 'octommerce.octommerce::lang.component.product_detail.param.id_param_desc',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
        ];
    }

    public function onRun()
    {
        $product = $this->loadProduct();

        if (!$product) {
            $this->setStatusCode(404);
            return $this->controller->run('404');
        }

        $this->product = $this->page['product'] = $product;

        $this->page->title = $product->name;
        $this->page->description = $product->description;
    }

    public function onChangeVariation()
    {

    }

    protected function loadProduct()
    {
        $slug = $this->property('slug');

        $product = ProductModel::whereSlug($slug)
            ->whereIsPublished(1)
            ->with('categories')
            ->with('lists')
            ->with('reviews')
            ->first();

        if ($product->type == 'variable') {

        }

        return $product;
    }

}