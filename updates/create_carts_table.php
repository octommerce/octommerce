<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCartsTable extends Migration
{

    public function up()
    {
        Schema::create('octommerce_octommerce_carts', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->text('session_id');
            $table->integer('user_id')->unsigned()->nullable();

            $table->timestamps();
        });

        Schema::create('octommerce_octommerce_cart_product', function($table)
        {
            $table->engine = 'InnoDB';

            // primary key
            $table->integer('cart_id')->unsigned();
            $table->integer('product_id')->unsigned();

            $table->integer('qty')->unsigned()->default(1);
            $table->decimal('price', 12, 2)->unsigned()->default(0);
            $table->decimal('discount', 12, 2)->unsigned()->default(0);
            $table->text('data')->nullable();

            $table->primary(['cart_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_cart_product');
        Schema::dropIfExists('octommerce_octommerce_carts');
    }

}
