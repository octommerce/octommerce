<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddExpiredAtToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_octommerce_orders', function(Blueprint $table) {
            $table->timestamp('expired_at')->nullable()->after('notes');
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_orders', function(Blueprint $table) {
            $table->dropColumn('expired_at');
        });
    }
}
