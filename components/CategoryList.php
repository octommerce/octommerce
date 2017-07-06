<?php namespace Octommerce\Octommerce\Components;

use Db;
use Cms\Classes\Page;
use Octommerce\Octommerce\Models\Category;
use Cms\Classes\ComponentBase;

class CategoryList extends ComponentBase
{
    /**
     * @var Collection A collection of categories to display
     */
    public $categories;

    /**
     * @var string Reference to the page name for linking to categories.
     */
    public $categoryPage;

    /**
     * @var string Reference to the current category slug.
     */
    public $currentCategorySlug;

    public function componentDetails()
    {
        return [
            'name'        => 'octommerce.octommerce::lang.component.category_list.name',
            'description' => 'octommerce.octommerce::lang.component.category_list.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'octommerce.octommerce::lang.component.category_list.param.slug',
                'description' => 'octommerce.octommerce::lang.component.category_list.param.slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string',
            ],
            'displayEmpty' => [
                'title'       => 'octommerce.octommerce::lang.component.category_list.param.display_empty',
                'description' => 'octommerce.octommerce::lang.component.category_list.param.display_empty_description',
                'type'        => 'checkbox',
                'default'     => 0,
            ],
            'categoryPage' => [
                'title'       => 'octommerce.octommerce::lang.component.category_list.param.category_page',
                'description' => 'octommerce.octommerce::lang.component.category_list.param.category_page_description',
                'type'        => 'dropdown',
                'emptyOption' => '- Default -',
                'group'       => 'Links',
            ],
        ];
    }

    public function getCategoryPageOptions()
    {
        return array_merge([null => '- Default -'], Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName'));
    }

    public function onRun()
    {
        $this->currentCategorySlug = $this->page['currentCategorySlug'] = $this->property('slug');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->categories = $this->page['categories'] = $this->loadCategories();
    }

    protected function loadCategories()
    {
        $categories = Category::orderBy('name');

        if (!$this->property('displayEmpty')) {
            $categories->whereExists(function($query) {
                $prefix = Db::getTablePrefix();

                $query
                    ->select(Db::raw(1))
                    ->from('octommerce_octommerce_category_product')
                    ->join('octommerce_octommerce_products', 'octommerce_octommerce_products.id', '=', 'octommerce_octommerce_category_product.product_id')
                    ->whereNotNull('octommerce_octommerce_products.is_published')
                    ->where('octommerce_octommerce_products.is_published', '=', 1)
                    ->whereRaw($prefix.'octommerce_octommerce_categories.id = '.$prefix.'octommerce_octommerce_category_product.category_id')
                ;
            });
        }

        $categories = $categories->getNested();

        /*
         * Add a "url" helper attribute for linking to each category
         */
        return $this->linkCategories($categories);
    }

    protected function linkCategories($categories)
    {
        return $categories->each(function($category) {
            $category->setUrl($this->categoryPage, $this->controller);

            $category->setPage($this->categoryPage);

            if ($category->children) {
                $this->linkCategories($category->children);
            }
        });
    }

}
