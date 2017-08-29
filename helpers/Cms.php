<?php namespace Octommerce\Octommerce\Helpers;

use Yaml;
use File;
use Flash;
use Cms\Classes\Page;
use Octommerce\Octommerce\Models\Settings;


/**
 * Cms helper
 */
class Cms
{
    use \October\Rain\Support\Traits\Singleton;

    public $pages;

    public function install()
    {
        $configFile = plugins_path('/octommerce/octommerce/config/cms_pages.yaml');
        $this->pages = Yaml::parse(File::get($configFile));

        $missingPages = $this->getMissingPages();

        foreach ($missingPages as $key => $missingPage) {
            $this->setPage($key, $missingPage);
        }

        // Flash::success('Installed successfully.');
    }

    public function getMissingPages()
    {
        $settings = Settings::instance();

        $missingPages = [];

        foreach ($this->pages as $key => $page) {

            if (! is_null($settings->{'cms_' . $key . '_page'}))
                continue;

            $missingPages[$key] = $page;
        }

        return $missingPages;
    }

    public function setPage($key, $page)
    {
        try {
            $markup = "<h1>{{ this.page.title }}</h1>\n";
            // $markup .= "{% component '{$page['component']}' %}";

            $page = new Page($page);
            $page->layout = 'default';
            $page->markup = $markup;
            $page->save();

            // Settings::set('cms_' . $key . '_page', $page->baseFileName);
        }
        catch (\Exception $e) {
            Flash::warning($e->getMessage());
        }
    }
}