<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddDiscountColumnOnCartsTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_octommerce_carts', function(Blueprint $table) {
            $table->decimal('discount', 12, 2)->unsigned()->nullable()->after('user_id');
            $table->text('discount_information')->nullable()->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_carts', function(Blueprint $table) {
            $table->dropColumn('discount');
            $table->dropColumn('discount_information');
        });
    }
}
