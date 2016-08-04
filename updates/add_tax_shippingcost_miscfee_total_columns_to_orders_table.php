<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddTaxShippingCostMiscFeeTotalColumnstToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_octommerce_orders', function(Blueprint $table) {
            $table->decimal('total', 12, 2)->unsigned()->default(0)->after('discount');
            $table->decimal('misc_fee', 12, 2)->unsigned()->default(0)->after('discount');
            $table->decimal('shipping_cost', 12, 2)->unsigned()->default(0)->after('discount');
            $table->decimal('tax', 12, 2)->unsigned()->default(0)->after('discount');
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_orders', function(Blueprint $table) {
            $table->dropColumn(['tax', 'shipping_cost', 'misc_fee', 'total']);
        });
    }
}
