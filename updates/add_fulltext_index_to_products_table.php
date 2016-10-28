<?php namespace Octommerce\Octommerce\Updates;

use DB;
use Schema;
use October\Rain\Database\Updates\Migration;

class AddFulltextIndexToProductsTable extends Migration
{

    public function up()
    {
        DB::statement('ALTER TABLE octommerce_octommerce_products ADD FULLTEXT (name)');
        DB::statement('ALTER TABLE octommerce_octommerce_products ADD FULLTEXT (description)');
        DB::statement('ALTER TABLE octommerce_octommerce_products ADD FULLTEXT (sku)');
        DB::statement('ALTER TABLE octommerce_octommerce_products ADD FULLTEXT (keywords)');
    }

    public function down()
    {
        Schema::table('octommerce_octommerce_products', function($table) {
            $table->dropIndex(['name', 'description', 'sku', 'keywords']);
        });
    }
}