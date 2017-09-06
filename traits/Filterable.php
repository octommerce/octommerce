<?php namespace Octommerce\Octommerce\Traits;

use Octommerce\Octommerce\Contracts\QueryFilters;

trait Filterable
{
    /**
     * Filter a result set.
     *
     * @param  Builder      $query
     * @param  QueryFilters $filters
     * @return Builder
     */
    public function scopeFilters($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }

}
