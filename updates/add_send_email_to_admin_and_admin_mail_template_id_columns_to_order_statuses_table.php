<?php namespace Octommerce\Octommerce\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddSendEmailToAdminAndAdminMailTemplateIdColumnsToOrderStatusesTable extends Migration
{
    public function up()
    {
        Schema::table('octommerce_octommerce_order_statuses', function(Blueprint $table) {
            $table->boolean('send_email_to_admin')->default(0)->after('send_email');
            $table->integer('admin_mail_template_id')->unsigned()->nullable()->after('mail_template_id');
        });
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_order_statuses', function(Blueprint $table) {
            $table->dropColumn('send_email_to_admin');
            $table->dropColumn('admin_mail_template_id');
        });
    }
}
