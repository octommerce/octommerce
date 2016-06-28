<?php namespace Octommerce\Octommerce\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Reports Back-end Controller
 */
class Reports extends Controller
{

    public $implement = [
        'Backend.Behaviors.ListController'
    ];

    public $listConfig = ['products' => 'config_list.yaml'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Octommerce.Octommerce', 'commerce', 'reports');
    }

    public function index()
    {
        $this->asExtension('ListController')->index();
        $this->bodyClass = 'compact-container';
    }
}