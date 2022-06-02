<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Railroad\Railforums\Services\ConfigService;

class AddIndexesToCoreTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ((ConfigService::$databaseConnectionName != ConfigService::$connectionMaskPrefix . 'testbench') || (config('database.connections.' . config('database.default') . '.database') === ':memory:')) {
            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tableCategories, function (Blueprint $table) {
                    $table->index('title');
                    $table->index('slug');
                    $table->index('weight');
                    $table->index('created_at');
                    $table->index('updated_at');
                    $table->index('deleted_at');
                    $table->index('brand');
                    $table->index('topic');
                    $table->index('icon');
                });

            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tablePosts, function (Blueprint $table) {
                    $table->index('edited_on');
                    $table->index('created_at');
                    $table->index('updated_at');
                    $table->index('deleted_at');
                });

            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tablePostLikes, function (Blueprint $table) {
                    $table->index('created_at');
                    $table->index('updated_at');
                });

            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tablePostReports, function (Blueprint $table) {
                    $table->index('reported_on');
                    $table->index('created_at');
                    $table->index('updated_at');
                });

            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tableSearchIndexes, function (Blueprint $table) {
                    $table->index('created_at');
                    $table->index('updated_at');
                });

            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tableThreads, function (Blueprint $table) {
                    $table->index('category_id');
                    $table->index('author_id');
                    $table->index('slug');
                    $table->index('pinned');
                    $table->index('locked');
                    $table->index('state');
                    $table->index('post_count');
                    $table->index('published_on');
                    $table->index('created_at');
                    $table->index('updated_at');
                    $table->index('deleted_at');
                });

            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tableThreadFollows, function (Blueprint $table) {
                    $table->index('followed_on');
                    $table->index('created_at');
                    $table->index('updated_at');
                });

            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tableThreadReads, function (Blueprint $table) {
                    $table->index('created_at');
                    $table->index('updated_at');
                });

            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tableUserSignatures, function (Blueprint $table) {
                    $table->index('brand');
                    $table->index('created_at');
                    $table->index('updated_at');
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
        if ((ConfigService::$databaseConnectionName != ConfigService::$connectionMaskPrefix . 'testbench') || (config('database.connections.' . config('database.default') . '.database') === ':memory:')) {
            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tableCategories, function (Blueprint $table) {

                    $table->dropIndex('title');
                    $table->dropIndex('slug');
                    $table->dropIndex('weight');
                    $table->dropIndex('created_at');
                    $table->dropIndex('updated_at');
                    $table->dropIndex('deleted_at');
                    $table->dropIndex('brand');
                    $table->dropIndex('topic');
                    $table->dropIndex('icon');

                });

            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tablePosts, function (Blueprint $table) {
                    $table->dropIndex('edited_on');
                    $table->dropIndex('created_at');
                    $table->dropIndex('updated_at');
                    $table->dropIndex('deleted_at');
                });

            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tablePostLikes, function (Blueprint $table) {
                    $table->dropIndex('created_at');
                    $table->dropIndex('updated_at');
                });

            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tablePostReports, function (Blueprint $table) {
                    $table->dropIndex('reported_on');
                    $table->dropIndex('created_at');
                    $table->dropIndex('updated_at');
                });

            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tableSearchIndexes, function (Blueprint $table) {
                    $table->dropIndex('created_at');
                    $table->dropIndex('updated_at');
                });

            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tableThreads, function (Blueprint $table) {
                    $table->dropIndex('category_id');
                    $table->dropIndex('author_id');
                    $table->dropIndex('slug');
                    $table->dropIndex('pinned');
                    $table->dropIndex('locked');
                    $table->dropIndex('state');
                    $table->dropIndex('post_count');
                    $table->dropIndex('published_on');
                    $table->dropIndex('created_at');
                    $table->dropIndex('updated_at');
                    $table->dropIndex('deleted_at');
                });

            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tableThreadFollows, function (Blueprint $table) {
                    $table->dropIndex('followed_on');
                    $table->dropIndex('created_at');
                    $table->dropIndex('updated_at');
                });

            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tableThreadReads, function (Blueprint $table) {
                    $table->dropIndex('created_at');
                    $table->dropIndex('updated_at');
                });

            Schema::connection(ConfigService::$databaseConnectionName)
                ->table(ConfigService::$tableUserSignatures, function (Blueprint $table) {
                    $table->dropIndex('brand');
                    $table->dropIndex('created_at');
                    $table->dropIndex('updated_at');
                });
        }
    }
}
