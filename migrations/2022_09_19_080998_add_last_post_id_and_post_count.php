<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Railroad\Railforums\Services\ConfigService;

class AddLastPostIdAndPostCount extends Migration
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
            if(Schema::connection($railforumsConnectionName)->hasColumn(ConfigService::$tableCategories, 'last_post_id')) {
                Schema::connection($railforumsConnectionName)
                    ->table(ConfigService::$tableCategories, function (Blueprint $table) {
                        $table->dropColumn('last_post_id');
                    });
            }

            if(Schema::connection($railforumsConnectionName)->hasColumn(ConfigService::$tableThreads, 'last_post_id')) {
                Schema::connection($railforumsConnectionName)
                    ->table(ConfigService::$tableThreads, function (Blueprint $table) {
                        $table->dropColumn('last_post_id');
                    });
            }
            if(Schema::connection($railforumsConnectionName)->hasColumn(ConfigService::$tableCategories, 'post_count')) {
                Schema::connection($railforumsConnectionName)
                    ->table(ConfigService::$tableCategories, function (Blueprint $table) {
                        $table->dropColumn('post_count');
                    });
            }
            if(Schema::connection($railforumsConnectionName)->hasColumn(ConfigService::$tableThreads, 'post_count')) {
                Schema::connection($railforumsConnectionName)
                    ->table(ConfigService::$tableThreads, function (Blueprint $table) {
                        $table->dropColumn('post_count');
                    });
            }

            Schema::connection($railforumsConnectionName)
                ->table(ConfigService::$tableCategories, function (Blueprint $table) {
                    $table->integer('last_post_id')
                        ->nullable();
                    $table->integer('post_count')
                        ->nullable();
                });
            Schema::connection($railforumsConnectionName)
                ->table(ConfigService::$tableThreads, function (Blueprint $table) {
                    $table->integer('last_post_id')
                        ->nullable();
                    $table->integer('post_count')
                        ->nullable();
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
        foreach (config('railforums.brand_database_connection_names') as $brand => $dbConnectionName) {
            $railforumsConnectionName = config('railforums.brand_database_connection_names')[$brand];
            Schema::connection($railforumsConnectionName)
                ->table(ConfigService::$tableCategories, function (Blueprint $table) {
                    $table->dropColumn('last_post_id');
                    $table->dropColumn('post_count');
                });

            Schema::connection($railforumsConnectionName)
                ->table(ConfigService::$tableThreads, function (Blueprint $table) {
                    $table->dropColumn('last_post_id');
                    $table->dropColumn('post_count');
                });
        }
    }
}
