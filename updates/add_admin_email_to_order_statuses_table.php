<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddAdminEmailToOrderStatusesTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_octommerce_order_statuses', function(Blueprint $table) {
            $table->string('admin_email')->nullable()->after('send_email_to_admin');
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_order_statuses', function(Blueprint $table) {
            $table->dropColumn('admin_email');
        });
    }
}
