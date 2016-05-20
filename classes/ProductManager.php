<?php namespace Octommerce\Octommerce\Classes;

use Carbon\Carbon;

class ProductManager
{
	use \October\Rain\Support\Traits\Singleton;

	public $types = [];

	public function __construct()
	{
		//
	}

	public function init()
	{
		//
	}

	/**
	 * [registerType description]
	 * @param  [type] $className [description]
	 * @return [type]            [description]
	 */
	public function registerType($className)
    {
    	$typeObject = new $className;

        $this->types[$className] = $typeObject->details;
    }

    /**
     * [registerTypes description]
     * @param  [type] $array [description]
     * @return [type]        [description]
     */
    public function registerTypes($array)
    {
    	foreach($array as $className) {
    		$this->registerType($className);
    	}
    }

    public function addCustomFields($form)
    {
    	foreach($this->types as $class => $type) {

    		$typeObject = new $class;

            $fields = $typeObject->registerFields() ?: [];

    		foreach($fields as $key => $typeProperty) {

	    		$typeProperty['span'] = $typeProperty['span'] ?: 'auto';
                $typeProperty['tab'] = $typeProperty['tab'] ?: 'Custom Fields';

	    		$typeProperty['trigger'] = [
		            'action' => 'show',
		            'field' => 'type',
		            'condition' => 'value[' . $type['code'] . ']',
	    		];

    			$form->addFields([
		            'options[' . $type['code'] . '][' . $key . ']' => $ruleProperty,
		        ]);
    		}
    	}
    }

    public function findTypeByCode($code)
    {
    	foreach($this->types as $className => $type) {
    		if($type['code'] == $code) {
    			return new $className;
    		}
    	}
    }

}