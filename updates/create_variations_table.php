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
            $table->integer('product_id')->unsigned()->nullable()->index();
            $table->integer('variation_group_id')->unsigned()->nullable()->index();
            $table->string('label');
            $table->decimal('price', 10, 2)->unsigned()->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_variations');
    }
}
