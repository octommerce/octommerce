<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddParentIdToOrderStatusesTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_octommerce_order_statuses', function(Blueprint $table) {
            $table->string('parent_code')->nullable();
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_order_statuses', function(Blueprint $table) {
            $table->dropColumn('parent_code');
        });
    }
}
