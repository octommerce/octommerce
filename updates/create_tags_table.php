<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateTagsTable extends Migration
{
    public function up()
    {
        Schema::create('octommerce_octommerce_tags', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 25)->nullable();
            $table->string('slug', 27)->nullable()->index();
            $table->timestamps();
        });

        Schema::create('octommerce_octommerce_product_tag', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('tag_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->index(['tag_id', 'product_id']);
            $table->foreign('tag_id')->references('id')->on('octommerce_octommerce_tags')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('octommerce_octommerce_products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_product_tag');
        Schema::dropIfExists('octommerce_octommerce_tags');
    }
}
