<?php namespace Octommere\Octommerce\Updates;

use October\Rain\Database\Updates\Seeder;
use Octommerce\Octommerce\Models\Category;

class SeedCurrenciesTable extends Seeder
{

    public function run()
    {
        Category::create([
            'name' => 'Sample Category',
        ]);
    }

}