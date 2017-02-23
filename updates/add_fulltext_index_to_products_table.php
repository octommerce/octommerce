<?php namespace Octommerce\Octommerce\Updates;

use DB;
use Schema;
use Exception;
use October\Rain\Database\Updates\Migration;

class AddFulltextIndexToProductsTable extends Migration
{

    public function up()
    {
        return; // Comment this line if you want to use fulltext search

        try {
            DB::statement('ALTER TABLE octommerce_octommerce_products ADD FULLTEXT (name)');
            DB::statement('ALTER TABLE octommerce_octommerce_products ADD FULLTEXT (description)');
            DB::statement('ALTER TABLE octommerce_octommerce_products ADD FULLTEXT (sku)');
            DB::statement('ALTER TABLE octommerce_octommerce_products ADD FULLTEXT (keywords)');
        } catch (Exception $e) {
            // MySQL is not supported. Please update to the newer version
        }
    }

    public function down()
    {
        return; // Comment this line if you want to use fulltext search

        try {
            Schema::table('octommerce_octommerce_products', function($table) {
                $table->dropIndex(['name', 'description', 'sku', 'keywords']);
            });
        } catch (Exception $e) {
            // MySQL is not supported. Please update to the newer version
        }
    }
}
