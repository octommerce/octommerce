<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddNewColumnsToProductListsTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_octommerce_product_lists', function(Blueprint $table) {
            $table->text('keywords')->after('description')->nullable();
            $table->text('excerpt')->after('description')->nullable();
            $table->text('content')->after('description')->nullable();
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_product_lists', function(Blueprint $table) {
            $table->dropColumn('keywords');
            $table->dropColumn('excerpt');
            $table->dropColumn('content');
        });
    }
}
