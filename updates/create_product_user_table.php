<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateProductUserTable extends Migration
{
    public function up()
    {
        Schema::create('octommerce_octommerce_product_user', function($table) {
            $table->integer('product_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();

            $table->primary(['product_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::drop('octommerce_octommerce_product_user');
    }
}
