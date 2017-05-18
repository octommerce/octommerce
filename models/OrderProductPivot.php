<?php namespace Octommerce\Octommerce\Models;

use October\Rain\Database\Pivot;

class OrderProductPivot extends Pivot 
{
    protected $jsonable = ['data'];
}
