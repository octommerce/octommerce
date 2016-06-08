<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddBrandIdToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_octommerce_products', function(Blueprint $table) {
            $table->integer('brand_id')->after('id')->unsigned()->nullable();
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_products', function(Blueprint $table) {
            $table->dropColumn('brand_id');
        });
    }
}
