<?php namespace Octommerce\Octommerce;

use System\Classes\PluginBase;

/**
 * Octommerce Plugin Information File
 */
class Plugin extends PluginBase
{

    public function register()
    {
        //
    }

    public function registerComponents()
    {
        return [
            'Octommerce\Octommerce\Components\Cart'     => 'cart',
        ];
    }

    public function registerPermissions()
    {
        return [
            //
        ];
    }

    public function registerMarkupTags()
    {
        return [
            //
        ];
    }

}
