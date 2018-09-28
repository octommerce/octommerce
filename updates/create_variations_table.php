<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateVariationsTable extends Migration
{
    public function up()
    {
        Schema::create('octommerce_octommerce_variations', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('size',8)->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });
        Schema::create('octommerce_octommerce_products_variations', function(Blueprint $table) {
            $table->integer('variation_id')->nullable();
            $table->integer('product_id')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_variations');
        Schema::dropIfExists('octommerce_octommerce_products_variations');
    }
}
