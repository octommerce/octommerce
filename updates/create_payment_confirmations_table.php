<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreatePaymentConfirmationsTable extends Migration
{
    public function up()
    {
        Schema::create('octommerce_octommerce_payment_confirmations', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('order_no');
            $table->string('email');
            $table->date('transfer_date');
            $table->string('account_owner');
            $table->string('bank_name');
            $table->string('transfer_amount');
            $table->string('destination_account');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('octommerce_octommerce_payment_confirmations');
    }
}
