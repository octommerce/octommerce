<?php namespace Octommere\Octommerce\Updates;

use October\Rain\Database\Updates\Seeder;
use Octommerce\Octommerce\Models\Currency;

class SeedCurrenciesTable extends Seeder
{

    public function run()
    {
        Currency::create([
            'name' => 'U.S. Dollar',
            'currency_code' => 'USD',
            'currency_symbol' => '$',
            'decimal_point' => '.',
            'thousand_separator' => ',',
            'place_symbol_before' => true,
            'is_enabled' => true,
            'is_primary' => true,
        ]);

        Currency::create([
            'name' => 'Euro',
            'currency_code' => 'EUR',
            'currency_symbol' => '€',
            'decimal_point' => '.',
            'thousand_separator' => ',',
            'place_symbol_before' => true,
            'is_enabled' => true,
            'is_primary' => false
        ]);

        Currency::create([
            'name' => 'Pound Sterling',
            'currency_code' => 'GBP',
            'currency_symbol' => '£',
            'decimal_point' => '.',
            'thousand_separator' => ',',
            'place_symbol_before' => true,
            'is_enabled' => true,
            'is_primary' => false
        ]);

        Currency::create([
            'name' => 'Australian Dollar',
            'currency_code' => 'AUD',
            'currency_symbol' => '$',
            'decimal_point' => '.',
            'thousand_separator' => ',',
            'place_symbol_before' => true,
            'is_enabled' => true,
            'is_primary' => false
        ]);

        Currency::create([
            'name' => 'Indonesian Rupiah',
            'currency_code' => 'IDR',
            'currency_symbol' => 'Rp',
            'decimal_point' => ',',
            'thousand_separator' => '.',
            'place_symbol_before' => true,
            'is_enabled' => true,
            'is_primary' => false
        ]);

        Currency::create([
            'name' => 'Singaporean Dollar',
            'currency_code' => 'SGD',
            'currency_symbol' => 'S$',
            'decimal_point' => '.',
            'thousand_separator' => ',',
            'place_symbol_before' => true,
            'is_enabled' => true,
            'is_primary' => true,
        ]);
    }

}