<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateReviewItemsTable extends Migration
{
    public function up()
    {
        Schema::create('octommerce_octommerce_review_items', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('review_id')->unsigned();
            $table->integer('review_type_id')->unsigned();
            $table->integer('rating')->unsigned();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_review_items');
    }
}
