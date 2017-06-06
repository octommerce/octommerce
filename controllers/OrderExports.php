<?php namespace Octommerce\Octommerce\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Order Exports Back-end Controller
 */
class OrderExports extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Octommerce.Octommerce', 'octommerce', 'orderexports');
    }
}