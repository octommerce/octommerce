<?php namespace Octommere\Octommerce\Updates;

use October\Rain\Database\Updates\Seeder;
use Octommerce\Octommerce\Models\Category;

class SeedCurrenciesTable extends Seeder
{

    public function run()
    {
        Category::create([
            'name' => 'Sample Category',
            'currency_symbol' => '$',
            'decimal_point' => '.',
            'thousand_separator' => ',',
            'place_symbol_before' => true,
            'is_enabled' => true,
            'is_primary' => true,
        ]);
    }

}