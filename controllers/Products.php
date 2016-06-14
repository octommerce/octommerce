<?php namespace Octommerce\Octommerce\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Octommerce\Octommerce\Models\Product;

/**
 * Products Back-end Controller
 */
class Products extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController',
        'Backend.Behaviors.ImportExportController',
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';
    public $importExportConfig = 'config_import_export.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Octommerce.Octommerce', 'products', 'products');
    }

    public function index()
    {
        $this->vars['totalProducts'] = number_format(Product::count());

        return $this->asExtension('ListController')->index();
    }

    public function create($context = null)
    {
        $this->bodyClass = 'compact-container';
        return $this->asExtension('FormController')->create($context);
    }

    public function update($recordId = null, $context = null)
    {
        $this->bodyClass = 'compact-container';
        return $this->asExtension('FormController')->update($recordId, $context);
    }

}
