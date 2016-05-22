<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateProductListsTable extends Migration
{

    public function up()
    {
        Schema::create('octommerce_octommerce_product_lists', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();

            $table->timestamps();
        });

        Schema::create('octommerce_octommerce_product_product_list', function($table)
        {
            $table->engine = 'InnoDB';

            $table->integer('list_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned()->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_product_lists');
    }

}
