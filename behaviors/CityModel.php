<?php namespace Octommerce\Octommerce\Behaviors;

use Octommerce\Octommerce\Classes\CityBehavior;

/**
 * City model extension
 *
 * Usage:
 *
 * In the model class definition:
 *
 *   public $implement = ['@Octommerce.Octommerce.Behaviors.CityModel'];
 *
 */
class CityModel extends CityBehavior
{

    /**
     * Constructor
     */
    public function __construct($model)
    {
        parent::__construct($model);

        $this->model->setTable('octommerce_octommerce_cities');
        $this->model->guard([]);
        $this->model->timestamps = false;

    }

}
