<?php namespace Octommerce\Octommerce\Components;

use Request;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Category;
use Octommerce\Octommerce\Models\Product;

class ProductList extends ComponentBase
{

    public $products;

    public function componentDetails()
    {
        return [
            'name'        => 'productList Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [
            'categorySlug' => [
                'title'       => 'octommerce.octommerce::lang.component.product_list.param.category_param_title',
                'description' => 'octommerce.octommerce::lang.component.product_list.param.category_param_desc',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
            'useCategoryFilter' => [
                'title'       => 'octommerce.octommerce::lang.component.product_list.param.usecategoryfilter_param_title',
                'description' => 'octommerce.octommerce::lang.component.product_list.param.usecategoryfilter_param_desc',
                'type'        => 'checkbox',
                'default'     => 0,
                'group'       => 'Filter',
            ],
            'categoryFilter' => [
                'title'       => 'octommerce.octommerce::lang.component.product_list.param.categoryfilter_param_title',
                'description' => 'octommerce.octommerce::lang.component.product_list.param.categoryfilter_param_desc',
                'type'        => 'string',
                'default'     => '',
                'group'       => 'Filter',
            ],
            'productPage' => [
                'title'       => 'octommerce.octommerce::lang.component.product_list.param.product_page_title',
                'description' => 'octommerce.octommerce::lang.component.product_list.param.product_page_desc',
                'type'        => 'dropdown',
                'default'     => 'products/:slug',
                'group'       => 'Products',
            ],
            'productPageSlug' => [
                'title'       => 'octommerce.octommerce::lang.component.product_list.param.product_page_id_title',
                'description' => 'octommerce.octommerce::lang.component.product_list.param.product_page_id_desc',
                'default'     => '{{ :slug }}',
                'type'        => 'string',
                'group'       => 'Products',
            ],
            'noProductsMessage' => [
                'title'        => 'octommerce.octommerce::lang.component.product_list.param.no_product_title',
                'description'  => 'octommerce.octommerce::lang.component.product_list.param.no_product_desc',
                'type'         => 'string',
                'default'      => 'No product found',
                'group'       => 'Products'
            ],
            'productsPerPage' => [
                'title'             => 'octommerce.octommerce::lang.component.product_list.param.products_per_page_title',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'octommerce.octommerce::lang.component.product_list.param.products_per_page_validation_message',
                'default'           => '10',
                'group'             => 'Pagination',
            ],
            'pageParam' => [
                'title'       => 'octommerce.octommerce::lang.component.product_list.param.page_param_title',
                'description' => 'octommerce.octommerce::lang.component.product_list.param.page_param_desc',
                'type'        => 'string',
                'default'     => ':page',
                'group'       => 'Pagination',
            ],
        ];
    }


    public function getProductPageOptions()
    {
        return [''=>'- none -'] + Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getSortOrderOptions()
    {
        return Product::$allowedSortingOptions;
    }

    public function onRun()
    {
        // Use strict method only to avoid conflicts whith other plugins
        $this->productPage = $this->property('productPage');

        $category = $this->category = $this->loadCategory();

        // Return error only if category filter is not used
        // if ($this->property('useCategoryFilter') == 0) {
        //     if (!$category) {
        //         $this->setStatusCode(404);
        //         return $this->controller->run('404');
        //     }
        // }

        $currentPage = post('page');
        $products = $this->products = $this->listProducts();

        /*
         * Pagination
         */
        if ($products) {
            $queryArr = [];
            $queryArr['page'] = '';
            $paginationUrl = Request::url() . '?' . http_build_query($queryArr);

            if ($currentPage > ($lastPage = $products->lastPage()) && $currentPage > 1) {
                return Redirect::to($paginationUrl . $lastPage);
            }

            $this->page['paginationUrl'] = $paginationUrl;
        }

        $this->noProductsMessage = $this->property('noProductsMessage');
        $this->productParam = $this->property('productParam');
        $this->productPageIdParam = $this->property('categorySlug');

    }

    public function listProducts()
    {
        $categories = $this->category ? $this->category->id : null;

        if ($this->property('useCategoryFilter') == 1 && $this->property('categoryFilter') != '') {
            $category = Category::whereSlug($this->property('categoryFilter'))->first();
            $categories = $category->id;
        }

        $products = Product::with('categories')
            ->whereIsPublished(1)
            ->paginate($this->property('productsPerPage'));

        return $products;
    }

    protected function loadCategory()
    {
        $category = Category::whereSlug($this->property('categorySlug'))->first();

        if (!$category) {
            return null;
        }

        $this->page->title = $category->name;

        return $category;
    }

}