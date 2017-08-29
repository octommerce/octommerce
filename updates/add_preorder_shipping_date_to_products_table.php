<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddPreorderShippingDateToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_octommerce_products', function(Blueprint $table) {
            $table->date('preorder_shipping_date')->after('stock_status')->nullable();
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_products', function(Blueprint $table) {
            $table->dropColumn('preorder_shipping_date');
        });
    }
}
