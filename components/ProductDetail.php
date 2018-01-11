<?php namespace Octommerce\Octommerce\Components;

use Auth;
use Flash;
use ApplicationException;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Product as ProductModel;

class ProductDetail extends ComponentBase
{
    public $product;

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

    public function onRemindMe()
    {
        $product = ProductModel::find(post('product_id'));

        if (is_null($product)) throw new ApplicationException('Product not found');

        $product->setReminderToUser(Auth::getUser());

        Flash::success('We will notify you when the stock is ready');
    }

    protected function loadProduct()
    {
        $slug = $this->property('slug');

        $product = ProductModel::whereSlug($slug)
            ->whereIsPublished(1)
            ->with('categories')
            ->with('lists')
            ->with('cross_sells')
            ->with('reviews')
            ->first();

        // if ($product->type == 'variable') {
        //
        // }

        return $product;
    }
}
