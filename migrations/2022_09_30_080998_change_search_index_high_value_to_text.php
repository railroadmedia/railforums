<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Railroad\Railforums\Services\ConfigService;

class ChangeSearchIndexHighValueToText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (config('railforums.brand_database_connection_names') as $brand => $dbConnectionName) {
            $railforumsConnectionName = config('railforums.brand_database_connection_names')[$brand];

            Schema::connection($railforumsConnectionName)
                ->table(
                    ConfigService::$tableSearchIndexes,
                    function (Blueprint $table) use($railforumsConnectionName) {
                        DB::connection($railforumsConnectionName)
                            ->statement(
                                'ALTER TABLE ' .
                                ConfigService::$tableSearchIndexes .
                                ' MODIFY high_value TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;'
                            );
                    }
                );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (config('railforums.brand_database_connection_names') as $brand => $dbConnectionName) {
            $railforumsConnectionName = config('railforums.brand_database_connection_names')[$brand];
            Schema::connection($railforumsConnectionName)
                ->table(
                    ConfigService::$tableSearchIndexes,
                    function (Blueprint $table) use($railforumsConnectionName) {
                        DB::connection($railforumsConnectionName)
                            ->statement(
                                'ALTER TABLE ' .
                                ConfigService::$tableSearchIndexes .
                                ' MODIFY `high_value` `high_value` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;'
                            );
                    }
                );
        }
    }
}
