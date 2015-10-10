<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCategoriesTable extends Migration
{

    public function up()
    {
        Schema::create('octommerce_octommerce_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->text('keywords')->nullable();

            $table->integer('parent_id')->unsigned()->index()->nullable();
            $table->integer('nest_left');
            $table->integer('nest_right');
            $table->integer('nest_depth')->nullable();

            $table->timestamps();
        });

        Schema::create('octommerce_octommerce_category_product', function($table)
        {
            $table->engine = 'InnoDB';

            $table->integer('product_id')->unsigned();
            $table->integer('category_id')->unsigned();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_categories');
    }

}
