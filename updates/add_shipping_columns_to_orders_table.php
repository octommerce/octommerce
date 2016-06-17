<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddShippingColumnsToOrdersTable extends Migration
{

    public function up()
    {
        Schema::table('octommerce_octommerce_orders', function($table)
        {
            // Shipping
            $table->integer('shipping_postcode')->unsigned()->nullable()->after('phone');
            $table->integer('shipping_state_id')->unsigned()->nullable()->after('phone');
            $table->integer('shipping_city_id')->unsigned()->nullable()->after('phone');
            $table->text('shipping_address')->nullable()->after('phone');
            $table->string('shipping_company')->nullable()->after('phone');
            $table->string('shipping_phone')->nullable()->after('phone');
            $table->integer('shipping_name')->unsigned()->nullable()->after('phone');
            $table->boolean('is_same_address')->default(false)->after('phone');
            // Biling
            $table->string('company')->nullable()->after('phone');
            $table->text('address')->nullable()->after('phone');
            $table->integer('city_id')->unsigned()->nullable()->after('phone');
            $table->integer('state_id')->unsigned()->nullable()->after('phone');
            $table->integer('postcode')->unsigned()->nullable()->after('phone');
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_orders', function($table) {
            $table->dropColumn('company');
            $table->dropColumn('address');
            $table->dropColumn('city_id');
            $table->dropColumn('state_id');
            $table->dropColumn('postcode');
            $table->dropColumn('is_same_address');
            $table->dropColumn('shipping_name');
            $table->dropColumn('shipping_phone');
            $table->dropColumn('shipping_company');
            $table->dropColumn('shipping_address');
            $table->dropColumn('shipping_city_id');
            $table->dropColumn('shipping_state_id');
            $table->dropColumn('shipping_postcode');
        });

    }
}