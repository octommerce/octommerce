<?php namespace Octommerce\Octommerce\Classes;

use Db;
use Octommerce\Octommerce\Contracts\QuerySort;

class ProductSort extends QuerySort
{
    public $implement;

    /**
     * @inheritDoc
     */
    protected $sortList = [
        'name asc'        => 'Name (ascending)',
        'name desc'       => 'Name (descending)',
        'createtAtAsc'    => 'Created (ascending)',
        'createtAtDesc'   => 'Created (descending)',
        'price asc'       => 'Price (ascending)',
        'price desc'      => 'Price (descending)',
        'random'          => 'Random',
        'sort_order asc'  => 'Reordered (ascending)',
        'sort_order desc' => 'Reordered (descending)',
        'salesAsc'        => 'Sales (ascending)',
        'salesDesc'       => 'Sales (descending)'
    ];

    /**
     * Order products by created_at asc
     */
    public function createtAtAsc()
    {
        $this->builder->orderBy('octommerce_octommerce_products.created_at', "asc");
    }

    /**
     * Order products by created_at desc
     */
    public function createtAtDesc()
    {
        $this->builder->orderBy('octommerce_octommerce_products.created_at', 'desc');
    }

    /**
     * Order random products
     */
    public function random()
    {
        $this->builder->orderByRaw('RAND()');
    }

    /**
     * Order products by sales (Ascending)
     */
    public function salesAsc()
    {
        $this->orderBySales();
    }

    /**
     * Order products by sales (Descending)
     */
    public function salesDesc()
    {
        $this->orderBySales('desc');
    }

    /**
     * Order products by sales
     *
     * @param string $direction
     * @return Builder
     */
    private function orderBySales($direction = 'asc')
    {
        return $this->builder->join(Db::raw("
                    (
                        select product_id as p_id, sum(qty) as sold
                        from octommerce_octommerce_order_product
                        where order_id in
                        (
                            select id from octommerce_octommerce_orders
                            where DATEDIFF(NOW(), created_at) <= 30
                            and status_code NOT IN (\"expired\", \"waiting\")
                        )
                        group by product_id order by sold ". $direction ."
                    ) op
                    "), 'octommerce_octommerce_products.id', '=', 'op.p_id');
    }
}
