<?php namespace Octommerce\Octommerce\Contracts;

use Input;
use Closure;
use Request;
use Illuminate\Database\Eloquent\Builder;

abstract class QuerySort
{
    use \October\Rain\Extension\ExtendableTrait;

    /**
     * Sort value
     *
     * @var string
     */
    protected $sort;

    /**
     * Available sort list
     *
     * @var array
     */
    protected $sortList = [];

    /**
     * The builder instance.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * Create a new QuerySort instance.
     *
     * @param string $sort
     */
    public function __construct(string $sort = null)
    {
        $this->sort = $sort;
        $this->extendableConstruct();
    }

    public static function extend(callable $callback)
    {
        self::extendableExtendCallback($callback);
    }

    /**
     * Get available sort list
     *
     * @return array
     */
    public function sortList()
    {
        return $this->sortList;
    }

    /**
     * Add custom sort
     *
     * @param array $sort
     * @param Closure $callback
     */
    public function addSort(array $sort, Closure $callback = null)
    {
        $key = camel_case(key($sort));
        $value = end($sort);

        $this->sortList[$key] = $value;

        if( ! is_callable($callback)) return;

        $this->addDynamicMethod($key, $callback);
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

    /**
     * Apply the sort to the builder.
     *
     * @param  Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        /**
         * pass the sort if there is no sort param in URL
         **/
        if ( ! $this->sort) {
            return $this->builder;
        }

        /**
         * pass the sort if the sort not available in sort list
         **/
        if ( ! key_exists($this->sort, $this->sortList())) {
            return $this->builder;
        }

        /**
         * Call the sort method (E.g for complex sort)
         **/
        if (method_exists($this, $this->sort)) {
            return $this->{$this->sort}();
        }

        /**
         * Checking if the sort method come from extension
         **/
        if (isset($this->extensionData['dynamicMethods'][$this->sort])) {
            $dynamicCallable = $this->extensionData['dynamicMethods'][$this->sort];

            if (is_callable($dynamicCallable)) {
                return call_user_func_array($dynamicCallable, []);
            }
        }

        list($column, $direction) = explode(' ', $this->sort);

        $this->builder->orderBy($column, $direction);
    }

    public function __call($name, $params)
    {
        return $this->extendableCall($name, $params);
    }
}
