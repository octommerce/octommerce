<?php namespace Octommerce\Octommerce\Classes;

use October\Rain\Extension\ExtensionBase;

/**
 * Base class for model behaviors.
 *
 * @package octommerce\octommerce
 * @author Surahman
 */
abstract class CityBehavior extends ExtensionBase
{

    /**
     * @var \October\Rain\Database\Model Reference to the extended model.
     */
    protected $model;

    /**
     * Constructor
     * @param \October\Rain\Database\Model $model The extended model.
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

}
