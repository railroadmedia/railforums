<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Railroad\Railforums\Services\ConfigService;

class ChangePostTextCollationForEmojiSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ConfigService::$databaseConnectionName)
            ->table(
                ConfigService::$tablePosts,
                function ($table) {

                    DB::connection(ConfigService::$databaseConnectionName)
                        ->statement(
                            'ALTER TABLE ' .
                            ConfigService::$tablePosts .
                            ' MODIFY content TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'
                        );

                    DB::connection(ConfigService::$databaseConnectionName)
                        ->statement(
                            'ALTER TABLE ' .
                            ConfigService::$tableThreads .
                            ' MODIFY title TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'
                        );
                }
            );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(ConfigService::$databaseConnectionName)
            ->table(
                ConfigService::$tablePosts,
                function ($table) {

                    DB::connection(ConfigService::$databaseConnectionName)
                        ->statement(
                            'ALTER TABLE ' .
                            ConfigService::$tablePosts .
                            ' MODIFY content TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci;'
                        );

                    DB::connection(ConfigService::$databaseConnectionName)
                        ->statement(
                            'ALTER TABLE ' .
                            ConfigService::$tableThreads .
                            ' MODIFY title TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci;'
                        );
                }
            );
    }
}
