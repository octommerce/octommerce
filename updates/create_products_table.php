<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateProductsTable extends Migration
{

    public function up()
    {
        Schema::create('octommerce_octommerce_products', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            // information
            $table->string('sku')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('keywords')->nullable();

            // inventory
            $table->boolean('manage_stock')->default(false);
            $table->enum('stock_status', ['in-stock', 'out-of-stock', 'pre-order'])->default('in-stock');
            $table->integer('qty')->unsigned()->nullable();

            // buy rules
            $table->integer('min_qty')->unsigned()->nullable();
            $table->integer('max_qty')->unsigned()->nullable();

            // price
            $table->decimal('price', 8, 2)->unsigned()->default('0.00');
            $table->integer('currency_id')->unsigned()->nullable();

            // discount
            $table->enum('discount_type', ['percent', 'price', 'shipping_percent', 'shipping_price'])->nullable();
            $table->decimal('discount_amount', 8, 2)->unsigned()->nullable();
            $table->decimal('sale_price', 8, 2)->unsigned()->nullable();

            // tax
            $table->integer('tax_id')->unsigned()->nullable();

            // weight
            $table->integer('weight')->unsigned()->nullable();
            $table->enum('weight_unit', ['gr', 'kg', 'ounce', 'pound'])->default('kg');

            // dimensions
            $table->decimal('width', 8, 2)->unsigned()->nullable();
            $table->decimal('height', 8, 2)->unsigned()->nullable();
            $table->decimal('length', 8, 2)->unsigned()->nullable();
            $table->enum('dimension_unit', ['mm', 'cm', 'inch', 'm'])->default('cm');

            // publication
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);

            // product_type
            $table->boolean('is_virtual')->default(false);
            $table->boolean('is_downloadable')->default(false);

            // availibily
            $table->dateTime('available_from')->nullable();
            $table->dateTime('available_to')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_products');
    }

}
