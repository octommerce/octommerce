<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddPriorityToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_octommerce_products', function(Blueprint $table) {
            $table->integer('priority')->after('brand_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_products', function(Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
}
