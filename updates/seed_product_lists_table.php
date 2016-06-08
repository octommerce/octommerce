<?php namespace Octommere\Octommerce\Updates;

use October\Rain\Database\Updates\Seeder;
use Octommerce\Octommerce\Models\ProductList;

class SeedProductListsTable extends Seeder
{

    public function run()
    {
        ProductList::create([
            'name'        => 'New',
            'description' => 'Show the new arrival products',
        ]);

        ProductList::create([
            'name'        => 'Featured',
            'description' => 'Your featured products.',
        ]);
    }

}