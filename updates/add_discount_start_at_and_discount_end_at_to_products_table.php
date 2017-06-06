<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddDiscountStartAtAndDiscountEndAtToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_octommerce_products', function(Blueprint $table) {
            $table->timestamp('discount_start_at')->after('discount_amount')->nullable();
            $table->timestamp('discount_end_at')->after('discount_start_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_products', function(Blueprint $table) {
            $table->dropColumn('discount_start_at');
            $table->dropColumn('discount_end_at');
        });
    }
}
