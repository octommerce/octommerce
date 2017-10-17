<?php namespace Octommerce\Octommerce\Models;

use Model;
use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;

/**
 * Brand Model
 */
class Brand extends Model
{
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'octommerce_octommerce_brands';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * Validation rules
     */
    public $rules = [
        'name' => 'required',
    ];

    /**
     * @var array Generate slugs for these attributes.
     */
    protected $slugs = ['slug' => 'name'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'products' => [
            'Octommerce\Octommerce\Models\Product',
        ],
        'products_count' => [
            'Octommerce\Octommerce\Models\Product',
            'count' => true
        ]
    ];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [
        'logo' => 'System\Models\File'
    ];
    public $attachMany = [
        'images' => ['System\Models\File'],
    ];

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function getPageUrlAttribute()
    {
        if (! $this->page && ! $this->page = Settings::get('cms_brand_page')) {
            return null;
        }

        $params = [
            'id'   => $this->id,
            'slug' => $this->slug,
        ];

        return CmsPage::url($this->page, $params);
    }

    /**
     * Handler for the pages.menuitem.getTypeInfo event.
     * Returns a menu item type information. The type information is returned as array
     * with the following elements:
     * - references - a list of the item type reference options. The options are returned in the
     *   ["key"] => "title" format for options that don't have sub-options, and in the format
     *   ["key"] => ["title"=>"Option title", "items"=>[...]] for options that have sub-options. Optional,
     *   required only if the menu item type requires references.
     * - nesting - Boolean value indicating whether the item type supports nested items. Optional,
     *   false if omitted.
     * - dynamicItems - Boolean value indicating whether the item type could generate new menu items.
     *   Optional, false if omitted.
     * - cmsPages - a list of CMS pages (objects of the Cms\Classes\Page class), if the item type requires a CMS page reference to
     *   resolve the item URL.
     * @param string $type Specifies the menu item type
     * @return array Returns an array
     */
    public static function getMenuTypeInfo($type)
    {
        $result = [];

        if ($type == 'all-brands') {
            $result = [
                'dynamicItems' => true
            ];
        }

        if ($result) {
            $theme = Theme::getActiveTheme();

            $pages = CmsPage::listInTheme($theme, true);
            $cmsPages = [];
            foreach ($pages as $page) {
                if (!$page->hasComponent('productList')) {
                    continue;
                }

                /*
                 * Component must use a category filter with a routing parameter
                 * eg: brandFilter = "{{ :somevalue }}"
                 */
                $properties = $page->getComponentProperties('productList');
                if (!isset($properties['brandFilter']) || !preg_match('/{{\s*:/', $properties['brandFilter'])) {
                    continue;
                }

                $cmsPages[] = $page;
            }

            $result['cmsPages'] = $cmsPages;
        }

        return $result;
    }

    /**
     * Handler for the pages.menuitem.resolveItem event.
     * Returns information about a menu item. The result is an array
     * with the following keys:
     * - url - the menu item URL. Not required for menu item types that return all available records.
     *   The URL should be returned relative to the website root and include the subdirectory, if any.
     *   Use the URL::to() helper to generate the URLs.
     * - isActive - determines whether the menu item is active. Not required for menu item types that
     *   return all available records.
     * - items - an array of arrays with the same keys (url, isActive, items) + the title key.
     *   The items array should be added only if the $item's $nesting property value is TRUE.
     * @param \RainLab\Pages\Classes\MenuItem $item Specifies the menu item.
     * @param \Cms\Classes\Theme $theme Specifies the current theme.
     * @param string $url Specifies the current page URL, normalized, in lower case
     * The URL is specified relative to the website root, it includes the subdirectory name, if any.
     * @return mixed Returns an array. Returns null if the item cannot be resolved.
     */
    public static function resolveMenuItem($item, $url, $theme)
    {
        $result = null;

        if ($item->type == 'all-brands') {
            $result = [
                'items' => []
            ];

            $brands = self::get();
            foreach ($brands as $brand) {
                $brandItem = [
                    'title' => $brand->name,
                    'url'   => self::getBrandPageUrl($item->cmsPage, $brand, $theme),
                    'mtime' => $brand->updated_at,
                ];

                $brandItem['isActive'] = $brandItem['url'] == $url;

                $result['items'][] = $brandItem;
            }
        }

        return $result;
    }

    /**
     * Returns URL of a brand page.
     */
    protected static function getBrandPageUrl($pageCode, $brand, $theme)
    {
        $page = CmsPage::loadCached($theme, $pageCode);
        if (!$page) return;

        $properties = $page->getComponentProperties('productList');
        if (!isset($properties['brandFilter'])) {
            return;
        }

        /*
         * Extract the routing parameter name from the brand filter
         * eg: {{ :someRouteParam }}
         */
        if (!preg_match('/^\{\{([^\}]+)\}\}$/', $properties['brandFilter'], $matches)) {
            return;
        }

        $paramName = substr(trim($matches[1]), 1);
        $url = CmsPage::url($page->getBaseFileName(), [$paramName => $brand->slug]);

        return $url;
    }

}