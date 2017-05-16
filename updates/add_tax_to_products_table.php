<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddTaxToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_octommerce_products', function(Blueprint $table) {
            $table->integer('tax')->after('sale_price')->nullable();
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_products', function(Blueprint $table) {
            $table->dropColumn('tax');
        });
    }
}
