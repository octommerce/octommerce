<?php namespace Octommerce\Octommerce\Contracts;

use Input;
use Request;
use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilters
{
    use \October\Rain\Extension\ExtendableTrait;

    public $implement;

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
        $this->extendableConstruct();
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
            /**
             * Checking if the filter method come from extension
             **/
            if (isset($this->extensionData['dynamicMethods'][$name])) {
                $dynamicCallable = $this->extensionData['dynamicMethods'][$name];

                if (is_callable($dynamicCallable)) {
                    return call_user_func_array($dynamicCallable, [$value]);
                }
            }

            if ( ! method_exists($this, $name)) {
                continue;
            }

            if (strlen($value)) {
                $this->$name($value);
            }
            else {
                $this->$name();
            }
        }

        return $this->builder;
    }

    /**
     * Add access to builder when performing complex query
     *
     * @return Builder 
     */
    public function builder()
    {
        return $this->builder;
    }

    public static function extend(callable $callback)
    {
        self::extendableExtendCallback($callback);
    }

    public function __call($name, $params)
    {
        return $this->extendableCall($name, $params);
    }
}
