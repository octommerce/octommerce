<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateHolidaysTable extends Migration
{
    public function up()
    {
        Schema::create('octommerce_octommerce_holidays', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->date('date');
            $table->string('name');
            $table->text('description')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_holidays');
    }
}
