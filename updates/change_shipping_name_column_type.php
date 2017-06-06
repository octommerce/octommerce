<?php namespace Octommere\Octommerce\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class ChangeShippingNameColumnType extends Migration
{
    public function up()
    {

        if(Schema::hasColumn('octommerce_octommerce_orders', 'shipping_name')) {
            Schema::table('octommerce_octommerce_orders', function($table)
            {
                $table->string('shipping_name', 50)->change();
            });
        }
    }

    public function down()
    {

    }
}