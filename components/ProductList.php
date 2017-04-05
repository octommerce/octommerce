<?php namespace Octommerce\Octommerce\Components;

use DB;
use Input;
use Request;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Category;
use Octommerce\Octommerce\Models\Product;
use Octommerce\Octommerce\Models\Brand;
use Octommerce\Octommerce\Models\ProductList as ProductListModel;

class ProductList extends ComponentBase
{
    public $list;
    public $brand;
    public $category;
    public $products;
    public $categories;
    public $filterList;
    public $searchQuery;

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
                'default'     => '',
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
            'sortOrder' => [
                'title'       => 'octommerce.octommerce::lang.component.product_list.param.sort_order_title',
                'description' => 'octommerce.octommerce::lang.component.product_list.param.sort_order_desc',
                'type'        => 'dropdown',
                'default'     => 'published_at desc'
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
                'default'     => '',
                'group'       => 'Pagination',
            ],
            'showFilterList' => [
                'title'       => 'Show filter list',
                'description' => 'It will show the available filter. Recommended for sidebar filter.',
                'type'        => 'checkbox',
                'default'     => false,
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
        $this->page['categories'] = $this->categories = $this->listCategories();
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
        $query = Product::with(['categories', 'brand', 'lists', 'images'])->published()->applyPriority();

        //
        // Filtering
        //
        $this->search($query, Input::get('q'));

        if ($this->property('categoryFilter') != '') {
            $this->filterByCategory($query, $this->property('categoryFilter'));
        }

        if ($this->property('listFilter') != '') {
            $this->filterByList($query, $this->property('listFilter'));
        }

        if ($this->property('brandFilter') != '') {
            $this->filterByBrand($query, $this->property('brandFilter'));
        }

        if ($this->property('hideOutOfStock')) {
            $query->available();
        }

        if ($this->property('showFilterList')) {
            $this->filterList = $this->getFilterList($query);
        }

        if ($this->property('sortOrder')) {
            $this->sortProducts($query, $this->property('sortOrder'));
        }

        return $query->paginate($this->property('productsPerPage'));
    }

    protected function getFilterList($originalQuery)
    {
        $items = [];

        // Category
        $query = clone $originalQuery;
        $query->join('octommerce_octommerce_category_product', 'product_id', '=', 'octommerce_octommerce_products.id')
            ->join('octommerce_octommerce_categories', 'category_id', '=', 'octommerce_octommerce_categories.id')
            ->select(DB::raw('octommerce_octommerce_categories.name, octommerce_octommerce_categories.slug, octommerce_octommerce_categories.parent_id, octommerce_octommerce_categories.nest_depth, category_id, count(*) as total'))
            ->orderBy('octommerce_octommerce_categories.nest_depth', 'asc')
            ->groupBy('category_id');
        // dd($query->get());

            // ->addSelect(DB::raw('*, count(*) as total'))
            // ->with('categories')
            // ->groupBy('category_id');

        $parents = [];

        foreach($query->get() as $row) {

            if ($row->category_id) {

                if (is_null($row->parent_id)) {
                    $parents[$row->id] = [
                        'name' => $row->name,
                        'count' => $row->total,
                    ];
                } else {
                    $parents[$row->parent_id]['children'][] = [
                        'name' => $row->name,
                        'count' => $row->total,
                    ];
                }

                // $items['categories'][] = [
                //     'name' => $row->name,
                //     'count' => $row->total,
                // ];
            }
        }

        $c = Category::with('children')->get();
        // dd($c);
        // dd($parents);


        // Brand
        $query = clone $originalQuery;
        $query->select(DB::raw('*, count(*) as total'))
            ->with('brand')
            ->groupBy('brand_id');

        foreach($query->get() as $product) {
            if ($product->brand) {
                $items['brands'][] = [
                    'name' => $product->brand->name,
                    'count' => $product->total,
                ];
            }
        }

        $items = collect($items)->sortByDesc('count');

        return $items;
    }

    protected function search(&$query, $searchQuery)
    {
        $this->searchQuery = $searchQuery = trim($searchQuery);

        if ($searchQuery) {

            // Simple search
            // $query->where(function($q) use ($searchQuery) {
            //     $q->where('octommerce_octommerce_products.name', 'like', '%' . $searchQuery . '%');
            // });

            // Fulltext Search
            // $query->whereRaw('MATCH (octommerce_octommerce_products.name) AGAINST (? IN NATURAL LANGUAGE MODE)', [$searchQuery]);

            // Using searchable trait
            $query->search($searchQuery);
        }
    }

    protected function filterByCategory(&$query, $slug)
    {
        $category = $this->category = Category::whereSlug($slug)->first();

        if ($category) {
            $query->whereHas('categories', function($q) use ($category) {
                $q->whereId($category->id);
            });
        }
    }

    public function filterByList(&$query, $slug)
    {
        $list = $this->list = ProductListModel::whereSlug($slug)->first();

        if ($list) {
            $query->whereHas('lists', function($q) use ($list) {
                $q->whereId($list->id);
            });
        }
    }

    protected function filterByBrand(&$query, $slug)
    {
        $brand = $this->brand = Brand::whereSlug($slug)->first();

        if ($brand) {
            $query->whereHas('brand', function($q) use ($brand) {
                $q->whereId($brand->id);
            });
        }
    }

    public function sortProducts(&$query, $sortOrder)
    {
        if (in_array($sortOrder, array_keys(Product::$allowedSortingOptions))) {
            $sortOrderArray = explode(" ", $sortOrder);

            if ($sortOrder == 'random') {
                $query->orderByRaw("RAND()");
            } elseif ($sortOrderArray[0] == 'sales') {

                // order by product sold qty for the last 30 days
                $query->join(DB::raw("
                    (
                        select product_id, sum(qty) as sold
                        from octommerce_octommerce_order_product
                        where order_id in
                        (
                            select id from octommerce_octommerce_orders
                            where DATEDIFF(NOW(), created_at) <= 30
                            and status_code NOT IN (\"expired\", \"waiting\")
                        )
                        group by product_id order by sold ". $sortOrderArray[1] ."
                    ) op
                    "), 'octommerce_octommerce_products.id', '=', 'op.product_id');

            } else {
                $query->orderBy($sortOrderArray[0], $sortOrderArray[1]);
            }
        }
    }

    /**
     * List all categories of products
     * @return Collection
     */
    public function listCategories()
    {
        $categories = Category::all();

        return $categories;
    }

    /**
     * Ajax Framework to handle on checked categories
     * @return Collection
     */
    public function onCheckedCategories()
    {
        $checkedCategories = post('categories');

        if(empty($checkedCategories)) {
            $getAllProducts = Product::all();
            $this->page['products'] = $getAllProducts;
        } else {
            $getProductsByCategories = Product::whereHas('categories', function($category) use ($checkedCategories) {
                $category->whereIn('slug', $checkedCategories);
            })->get();

            $this->page['products'] = $getProductsByCategories;
        }
    }

    /**
     * collect children ids recursively
     * @return array collection of childrena and parent ids
     */
    public function collectChildren($category)
    {
        $children = [];
        //push parent id
        array_push($children, $category->id);
        //push children id
        foreach($category->children as $child) {
            array_push($children, $child->id);
            if($child->children()->count()) {
                return array_merge($children, $this->collectChildren($child));
            }
        }
        return $children;
    }
}
