<?php namespace Octommerce\Octommerce\Contracts;

use Input;
use Request;
use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilters
{

    /**
     * The filters array.
     *
     * @var filters
     */
    protected $filters;

    /**
     * The builder instance.
     *
     * @var Builder
     */

    protected $builder;
    /**
     * Create a new QueryFilters instance.
     *
     * @param array $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Apply the filters to the builder.
     *
     * @param  Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->filters as $name => $value) {
            if (! method_exists($this, $name)) {
                continue;
            }
            if (strlen($value)) {
                $this->$name($value);
            } else {
                $this->$name();
            }
        }

        return $this->builder;
    }

}
