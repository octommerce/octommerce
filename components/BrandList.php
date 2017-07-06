<?php namespace Octommerce\Octommerce\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Octommerce\Octommerce\Models\Brand;

class BrandList extends ComponentBase
{
    /**
     * @var Collection A collection of brands to display
     */
    public $brands;

    /**
     * @var string Reference to the page name for linking to brands.
     */
    public $brandPage;

    /**
     * @var string Reference to the current brand slug.
     */
    public $currentBrandSlug;

    public function componentDetails()
    {
        return [
            'name'        => 'octommerce.octommerce::lang.component.brand_list.name',
            'description' => 'octommerce.octommerce::lang.component.brand_list.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'octommerce.octommerce::lang.component.brand_list.param.slug',
                'description' => 'octommerce.octommerce::lang.component.brand_list.param.slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string',
            ],
            'displayEmpty' => [
                'title'       => 'octommerce.octommerce::lang.component.brand_list.param.display_empty',
                'description' => 'octommerce.octommerce::lang.component.brand_list.param.display_empty_description',
                'type'        => 'checkbox',
                'default'     => 0,
            ],
            'brandPage' => [
                'title'       => 'octommerce.octommerce::lang.component.brand_list.param.brand_page',
                'description' => 'octommerce.octommerce::lang.component.brand_list.param.brand_page_description',
                'type'        => 'dropdown',
                'group'       => 'Links',
            ],
        ];
    }

    public function getBrandPaegOptions()
    {
        return array_merge([null => '- Default -'], Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName'));
    }

    public function onRun()
    {
        $this->currentBrandSlug = $this->page['currentBrandSlug'] = $this->property('slug');
        $this->brandPage = $this->page['brandPage'] = $this->property('brandPage');
        $this->brands = $this->page['brands'] = $this->loadBrands();
    }

    protected function loadBrands()
    {
        $query = Brand::orderBy('name');

        if (! $this->property('displayEmpty')) {
            $query->has('products');
        }

        $brands = $query->get();

        /*
         * Add a "url" helper attribute for linking to each brand
         */
        return $this->linkBrands($brands);
    }

    protected function linkBrands($brands)
    {
        return $brands->each(function($brand) {
            $brand->setPage($this->brandPage);
        });
    }
}
