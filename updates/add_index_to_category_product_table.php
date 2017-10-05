<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddIndexToCategoryProductTable extends Migration
{

    public function up()
    {
        Schema::table('octommerce_octommerce_category_product', function($table)
        {
            $table->integer('product_id')->unsigned()->index()->change();
            $table->integer('category_id')->unsigned()->index()->change();
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_category_product', function($table)
        {
            $table->integer('product_id')->unsigned()->change();
            $table->integer('category_id')->unsigned()->change();
        });
    }

}
