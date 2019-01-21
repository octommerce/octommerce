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

            // type
            $table->string('type')->default('simple');

            // For custom fields
            $table->text('options')->nullable();

            // If grouped product
            $table->integer('parent_id')->unsigned()->nullable();

            // inventory
            $table->boolean('manage_stock')->default(false);
            $table->enum('stock_status', ['in-stock', 'out-of-stock', 'pre-order'])->default('in-stock');
            $table->integer('qty')->unsigned()->nullable();
            $table->string('when_out_of_stock')->default('deny');

            // buy rules
            $table->integer('min_qty')->unsigned()->nullable();
            $table->integer('max_qty')->unsigned()->nullable();

            // price
            $table->decimal('price', 12, 2)->unsigned()->default('0.00');
            $table->string('currency_code', 10)->nullable();

            // discount
            $table->string('discount_type')->nullable();
            $table->decimal('discount_amount', 12, 2)->unsigned()->nullable();
            $table->decimal('sale_price', 12, 2)->unsigned()->nullable();

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

            $table->integer('sort_order')->unsigned()->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('octommerce_octommerce_product_up_sell', function($table)
        {
            $table->engine = 'InnoDB';

            $table->integer('product_id')->unsigned();
            $table->integer('up_sell_id')->unsigned();
        });

        Schema::create('octommerce_octommerce_product_cross_sell', function($table)
        {
            $table->engine = 'InnoDB';

            $table->integer('product_id')->unsigned();
            $table->integer('cross_sell_id')->unsigned();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_product_cross_sell');
        Schema::dropIfExists('octommerce_octommerce_product_up_sell');
        Schema::dropIfExists('octommerce_octommerce_products');
    }

}
