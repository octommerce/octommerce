<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateReviewsTable extends Migration
{

    public function up()
    {
        Schema::create('octommerce_octommerce_reviews', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('product_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();

            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->integer('rating')->unsigned()->nullable();

            $table->boolean('is_buyer')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_reviews');
    }

}
