<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddQtyBeforeToOrderProductTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_octommerce_order_product', function(Blueprint $table) {
            $table->integer('qty_before')->nullable()->after('qty');
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_order_product', function(Blueprint $table) {
            $table->dropColumn('qty_before');
        });
    }
}
