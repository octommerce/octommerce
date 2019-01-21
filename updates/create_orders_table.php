<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateOrdersTable extends Migration
{

    public function up()
    {
        Schema::create('octommerce_octommerce_orders', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('order_no');

            $table->integer('user_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->text('message')->nullable();

            $table->string('currency_code')->default('IDR');

            $table->decimal('subtotal', 12, 2)->unsigned()->default(0);
            $table->decimal('discount', 12, 2)->unsigned()->default(0);

            $table->string('status_code')->nullable();
            $table->timestamp('status_updated_at')->nullable();
            $table->boolean('is_followed_up')->default(false);
            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('octommerce_octommerce_order_product', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('order_id')->unsigned();
            $table->integer('product_id')->unsigned();

            $table->integer('qty')->unsigned()->default(1);
            $table->decimal('price', 12, 2)->unsigned()->default(0);
            $table->decimal('discount', 12, 2)->unsigned()->default(0);

            $table->string('name')->nullable();
            $table->text('data')->nullable();

            $table->primary(['order_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_order_product');
        Schema::dropIfExists('octommerce_octommerce_orders');
    }

}
