<?php namespace Octommerce\Octommerce\Components;

use Request;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Category;
use Octommerce\Octommerce\Models\Product;
use Octommerce\Octommerce\Models\Brand;
use Octommerce\Octommerce\Models\ProductList as ProductListModel;

class ProductList extends ComponentBase
{
    public $category;
    public $list;
    public $brand;
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
            'categoryFilter' => [
                'title'       => 'octommerce.octommerce::lang.component.product_list.param.categoryfilter_param_title',
                'description' => 'octommerce.octommerce::lang.component.product_list.param.categoryfilter_param_desc',
                'type'        => 'dropdown',
                'default'     => '',
                'group'       => 'Filter',
            ],
            'listFilter' => [
                'title'       => 'octommerce.octommerce::lang.component.product_list.param.listfilter_param_title',
                'description' => 'octommerce.octommerce::lang.component.product_list.param.listfilter_param_desc',
                'type'        => 'dropdown',
                'default'     => '',
                'group'       => 'Filter',
            ],
            'brandFilter' => [
                'title'       => 'octommerce.octommerce::lang.component.product_list.param.brandfilter_param_title',
                'description' => 'octommerce.octommerce::lang.component.product_list.param.brandfilter_param_desc',
                'type'        => 'dropdown',
                'default'     => '',
                'group'       => 'Filter',
            ],
            'hideOutOfStock' => [
                'title'        => 'octommerce.octommerce::lang.component.product_list.param.hide_out_of_stock_title',
                'description'  => 'octommerce.octommerce::lang.component.product_list.param.hide_out_of_stock_desc',
                'type'         => 'checkbox',
                'default'      => false,
                'group'        => 'Filter'
            ],
            'noProductsMessage' => [
                'title'        => 'octommerce.octommerce::lang.component.product_list.param.no_product_title',
                'description'  => 'octommerce.octommerce::lang.component.product_list.param.no_product_desc',
                'type'         => 'string',
                'default'      => 'No product found',
                'group'        => 'Filter'
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

    public function getCategoryFilterOptions()
    {
        return ['' => '- none -'] + Category::lists('name', 'slug');
    }

    public function getListFilterOptions()
    {
        return ['' => '- none -'] + ProductListModel::lists('name', 'slug');
    }

    public function getBrandFilterOptions()
    {
        return ['' => '- none -'] + Brand::lists('name', 'slug');
    }

    public function getSortOrderOptions()
    {
        return Product::$allowedSortingOptions;
    }

    public function onRun()
    {

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
        $query = Product::whereIsPublished(1)
            ->with('categories')
            ->with('lists');

        if ($this->property('categoryFilter') != '') {
            $category = $this->category = Category::whereSlug($this->property('categoryFilter'))->first();

            if ($category) {
                $query->whereHas('categories', function($q) use ($category) {
                    $q->whereId($category->id);
                });
            }
        }

        if ($this->property('listFilter') != '') {
            $list = $this->list = ProductListModel::whereSlug($this->property('listFilter'))->first();

            if ($list) {
                $query->whereHas('lists', function($q) use ($list) {
                    $q->whereId($list->id);
                });
            }
        }

        if ($this->property('brandFilter') != '') {
            $brand = $this->brand = Brand::whereSlug($this->property('brandFilter'))->first();

            if ($brand) {
                $query->whereHas('brand', function($q) use ($brand) {
                    $q->whereId($brand->id);
                });
            }
        }

        if ($this->property('hideOutOfStock')) {
            $query->available();
        }

        $products = $query->paginate($this->property('productsPerPage'));

        return $products;
    }

}