<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Railroad\Railforums\Services\ConfigService;

class RenameLegacyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (config('railforums.legacy_tables_mapping', []) as $tableName => $legacyTableName) {
            if (!Schema::connection(ConfigService::$databaseConnectionName)->hasTable($legacyTableName)) {
                Schema::connection(ConfigService::$databaseConnectionName)->rename($tableName, $legacyTableName);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (config('railforums.legacy_tables_mapping', []) as $tableName => $legacyTableName) {
            if (!Schema::connection(ConfigService::$databaseConnectionName)->hasTable($tableName)) {
                Schema::connection(ConfigService::$databaseConnectionName)->rename($legacyTableName, $tableName);
            }
        }
    }
}
