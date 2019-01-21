<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateProductAttributesTable extends Migration
{

    public function up()
    {
        Schema::create('octommerce_octommerce_product_attributes', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name');
            $table->string('type')->default('text');
            $table->text('options')->nullable();
            $table->text('default')->nullable();
            $table->timestamps();
        });

        Schema::create('octommerce_octommerce_product_product_attribute', function($table)
        {
            $table->engine = 'InnoDB';

            $table->integer('product_id')->unsigned()->nullable();
            $table->integer('attribute_id')->unsigned();

            $table->text('value')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_product_product_attribute');
        Schema::dropIfExists('octommerce_octommerce_product_attributes');
    }

}
