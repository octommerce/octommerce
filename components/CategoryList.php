<?php namespace Octommerce\Octommerce\Components;

use Octommerce\Octommerce\Models\Category;
use Cms\Classes\ComponentBase;

class CategoryList extends ComponentBase
{
    /**
     * @var Collection A collection of categories to display
     */
    public $categories;

    public function componentDetails()
    {
        return [
            'name'        => 'categoryList Component',
            'description' => 'No description provided yet...'
        ];
    }

    // public function defineProperties()
    // {
    //     return [
    //         'slug' => [
    //             'title'       => 'Slug',
    //             'default'     => '{{ :slug }}',
    //             'type'        => 'string'
    //         ],
    //         'displayEmpty' => [
    //             'title'       => 'rainlab.blog::lang.settings.category_display_empty',
    //             'description' => 'rainlab.blog::lang.settings.category_display_empty_description',
    //             'type'        => 'checkbox',
    //             'default'     => 0
    //         ],
    //         'categoryPage' => [
    //             'title'       => 'rainlab.blog::lang.settings.category_page',
    //             'description' => 'rainlab.blog::lang.settings.category_page_description',
    //             'type'        => 'dropdown',
    //             'default'     => 'blog/category',
    //             'group'       => 'Links',
    //         ],
    //     ];
    // }

    public function onRun()
    {
        // $this->currentCategorySlug = $this->page['currentCategorySlug'] = $this->property('slug');
        // $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->categories = $this->page['categories'] = $this->loadCategories();
    }

    protected function loadCategories()
    {
        $categories = Category::orderBy('name')->getNested();

        return $categories;
        /*
         * Add a "url" helper attribute for linking to each category
         */
        return $this->linkCategories($categories);
    }

    protected function linkCategories($categories)
    {
        return $categories->each(function($category) {
            $category->setUrl($this->categoryPage, $this->controller);

            if ($category->children) {
                $this->linkCategories($category->children);
            }
        });
    }

}