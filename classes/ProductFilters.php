<?php namespace Octommerce\Octommerce\Classes;

use Illuminate\Database\Eloquent\Builder;
use Octommerce\Octommerce\Contracts\QueryFilters;

class ProductFilters extends QueryFilters
{
    /**
     * Filter by category.
     *
     * @param  string $slug
     * @return Builder
     */
    public function category($slug)
    {
        return $this->builder->whereHas('categories', function($q) use ($slug) {
            $q->where('octommerce_octommerce_categories.slug', '=', $slug);
        });
    }

    /**
     * Filter by list.
     *
     * @param  string $slug
     * @return Builder
     */
    public function lists($slug)
    {
        return $this->builder->whereHas('lists', function($q) use ($slug) {
            $q->where('octommerce_octommerce_product_lists.slug', '=', $slug);
        });
    }

    /**
     * Filter by brand.
     *
     * @param  string $slug
     * @return Builder
     */
    public function brand($slug)
    {
        return $this->builder->whereHas('brand', function($q) use ($slug) {
            $q->where('octommerce_octommerce_brands.slug', '=', $slug);
        });
    }
}
