<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddEmailColumnOnPaymentConfirmationTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_octommerce_payment_confirmations', function(Blueprint $table) {
            $table->string('email')->after('order_no');
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_payment_confirmations', function(Blueprint $table) {
            $table->dropColumn('email');
        });
    }
}
