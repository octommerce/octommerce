<?php namespace Marthatilaar\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddProfileColumnsToUsersTable extends Migration
{

    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->integer('state_id')->unsigned()->nullable();
            $table->integer('city_id')->unsigned()->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('postcode')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn('state_id');
            $table->dropColumn('city_id');
            $table->dropColumn('phone');
            $table->dropColumn('address');
            $table->dropColumn('postcode');
        });
    }

}
