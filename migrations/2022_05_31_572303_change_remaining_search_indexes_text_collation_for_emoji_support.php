<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Railroad\Railforums\Services\ConfigService;

class ChangeRemainingSearchIndexesTextCollationForEmojiSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ((config()->get('database.default') != 'railforums_testbench') || (config('database.connections.' . config('database.default') . '.database') === ':memory:')) {
            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(
                    ConfigService::$tableSearchIndexes,
                    function ($table) {

                        DB::connection(ConfigService::$databaseConnectionName)
                            ->statement(
                                'ALTER TABLE ' .
                                ConfigService::$tableSearchIndexes .
                                ' MODIFY medium_value TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'
                            );

                        DB::connection(ConfigService::$databaseConnectionName)
                            ->statement(
                                'ALTER TABLE ' .
                                ConfigService::$tableSearchIndexes .
                                ' MODIFY low_value TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'
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
        if ((config()->get('database.default') != 'railforums_testbench') || (config('database.connections.' . config('database.default') . '.database') === ':memory:')) {
            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(
                    ConfigService::$tablePosts,
                    function ($table) {

                        DB::connection(ConfigService::$databaseConnectionName)
                            ->statement(
                                'ALTER TABLE ' .
                                ConfigService::$tableSearchIndexes .
                                ' MODIFY medium_value TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci;'
                            );

                        DB::connection(ConfigService::$databaseConnectionName)
                            ->statement(
                                'ALTER TABLE ' .
                                ConfigService::$tableSearchIndexes .
                                ' MODIFY low_value TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci;'
                            );
                    }
                );
        }
    }
}
