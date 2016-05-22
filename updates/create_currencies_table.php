<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCurrenciesTable extends Migration
{

    public function up()
    {
        Schema::create('octommerce_octommerce_currencies', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('currency_code', 10)->index()->nullable();
            $table->string('currency_symbol', 10)->nullable();
            $table->string('decimal_point', 1)->nullable();
            $table->string('thousand_separator', 1)->nullable();
            $table->boolean('place_symbol_before')->default(true);
            $table->boolean('is_enabled')->default(false);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_currencies');
    }

}
