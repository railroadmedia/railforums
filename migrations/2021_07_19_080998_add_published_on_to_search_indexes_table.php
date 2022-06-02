<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Railroad\Railforums\Services\ConfigService;

class AddPublishedOnToSearchIndexesTable extends Migration
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

                    $table->dateTime('published_on')
                        ->nullable()
                        ->index();

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

                    $table->dropColumn('published_on');

                });
        }
    }
}
