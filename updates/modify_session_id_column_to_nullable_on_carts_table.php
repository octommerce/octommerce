<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class ModifySessionIdColumnToNullableOnCartsTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_octommerce_carts', function(Blueprint $table) {
            $table->text('session_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_carts', function(Blueprint $table) {
            $table->text('session_id')->change();
        });
    }
}
