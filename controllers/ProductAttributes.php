<?php namespace Octommerce\Octommerce\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Product Attributes Back-end Controller
 */
class ProductAttributes extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Octommerce.Octommerce', 'products', 'attributes');
    }

    public function formBeforeSave($model)
    {
        $data = post('ProductAttribute');

        $model->default = isset($data['_default'][$data['type']]) ? $data['_default'][$data['type']] : null;
        $model->options = isset($data['_options'][$data['type']]) ? $data['_options'][$data['type']] : null;
    }

    public function formExtendFields($host, $fields)
    {
        $type = $host->model->type;

        isset($fields['_default[' . $type . ']']) ? $fields['_default[' . $type . ']']->value = $host->model->default : '';

        if(count($host->model->options)) {
            foreach($host->model->options as $key => $value) {
                isset($fields['_options[' . $type . '][' . $key . ']']) ? $fields['_options[' . $type . '][' . $key . ']']->value = $value : '';
            }
        }
    }
}