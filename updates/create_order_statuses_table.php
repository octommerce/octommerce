<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateOrderStatusesTable extends Migration
{
    public function up()
    {
        Schema::create('octommerce_octommerce_order_statuses', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->string('code')->unique();
            $table->string('name');
            $table->string('description')->nullable();

            $table->string('color')->nullable();

            $table->boolean('is_active')->default(1);

            $table->boolean('send_email')->default(0);
            $table->boolean('attach_pdf')->default(0);

            $table->string('mail_template_id')->unsigned()->nullable();

            $table->integer('sort_order')->unsigned()->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_order_statuses');
    }
}
