<?php namespace Octommerce\Octommerce\Models;

use Backend\Models\ImportModel;

/**
 * HolidayImport Model
 */
class HolidayImport extends ImportModel
{
    /**
     * @var array The rules to be applied to the data.
     */
    public $rules = [];

    public function importData($results, $sessionKey = null)
    {
        //
    }
}