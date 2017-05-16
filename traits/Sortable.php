<?php namespace Octommerce\Octommerce\Traits;

use Octommerce\Octommerce\Contracts\QuerySort;

trait Sortable
{
    /**
     * Sort a result set.
     *
     * @param  Builder $query
     * @param  QuerySort $sort
     * @return Builder
     */
    public function scopeSort($query, QuerySort $sort)
    {
        return $sort->apply($query);
    }

}
