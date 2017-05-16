<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddTotalWeightColumnToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_octommerce_orders', function(Blueprint $table) {
            $table->integer('total_weight')->nullable()->unsigned()->after('message');
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_orders', function(Blueprint $table) {
            $table->dropColumn('total_weight');
        });
    }
}
