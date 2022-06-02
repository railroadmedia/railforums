<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Railroad\Railforums\Services\ConfigService;

class AddUniqueKeyToSearchIndexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('database.connections.' . config('database.default') . '.database') === ':memory:') {
            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tableSearchIndexes, function (Blueprint $table) {
                    $table->unique(['thread_id', 'post_id'], 'search_index_unique_id');
                });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (config('database.connections.' . config('database.default') . '.database') === ':memory:') {
            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tableSearchIndexes, function (Blueprint $table) {
                    $table->dropIndex('search_index_unique_id');
                });
        }
    }
}
