<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateOrderStatusLogsTable extends Migration
{
    public function up()
    {
        Schema::create('octommerce_octommerce_order_status_logs', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('order_id')->unsigned();
            $table->string('status_code');
            $table->text('data')->nullable();

            $table->timestamp('timestamp');
            $table->integer('admin_id')->unsigned()->nullable();
            $table->text('note')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_order_status_logs');
    }
}
