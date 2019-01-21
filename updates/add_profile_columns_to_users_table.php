<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddProfileColumnsToUsersTable extends Migration
{

    public function up()
    {   
        Schema::table('users', function($table)
        {
            if (!Schema::hasColumn('users', 'state_id')) {
                $table->integer('state_id')->unsigned()->nullable();
            }
            if (!Schema::hasColumn('users', 'city_id')) {
                $table->integer('city_id')->unsigned()->nullable();
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
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
