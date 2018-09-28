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
            $table->integer('product_id')->unsigned()->nullable();
            $table->string('attribute')->nullable();
            $table->string('size')->nullable();
            $table->longtext('value')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_variations');
    }
}
