<?php

namespace Railroad\Railforums\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Railroad\Railforums\Commands\CreateSearchIndexes;
use Railroad\Railforums\Decorators\ThreadDecorator;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Railforums\EventListeners\PostEventListener;
use Railroad\Railforums\Events\PostDeleted;

class ForumServiceProvider extends EventServiceProvider
{
    protected $listen = [
        PostDeleted::class => [
            PostEventListener::class . '@onPostDeleted',
        ]
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // publish config file
        $this->publishes(
            [
                __DIR__ . '/../config/railforums.php' => config_path('railforums.php'),
            ]
        );

        $this->setupConfig();

        if (ConfigService::$dataMode == 'host') {
            $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
        }

        $this->loadRoutesFrom(__DIR__ . '/../../routes/routes.php');

        $this->commands([
            CreateSearchIndexes::class
        ]);

        parent::boot();

        // merge in permissions settings
        config()->set(
            'permissions.role_abilities',
            array_merge(
                config()->get('permissions.role_abilities', []),
                config()->get('railforums.role_abilities', [])
            )
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Loads config data into config service
     */
    protected function setupConfig()
    {
        // database
        ConfigService::$databaseConnectionName = config('railforums.database_connection_name');
        ConfigService::$connectionMaskPrefix = config('railforums.connection_mask_prefix');
        ConfigService::$dataMode = config('railforums.data_mode');

        // tables
        ConfigService::$tablePrefix = config('railforums.table_prefix');
        ConfigService::$tableCategories = ConfigService::$tablePrefix . config('railforums.tables.categories');
        ConfigService::$tableThreads = ConfigService::$tablePrefix . config('railforums.tables.threads');
        ConfigService::$tableThreadFollows = ConfigService::$tablePrefix . config('railforums.tables.thread_follows');
        ConfigService::$tableThreadReads = ConfigService::$tablePrefix . config('railforums.tables.thread_reads');
        ConfigService::$tablePosts = ConfigService::$tablePrefix . config('railforums.tables.posts');
        ConfigService::$tablePostLikes = ConfigService::$tablePrefix . config('railforums.tables.post_likes');
        ConfigService::$tablePostReports = ConfigService::$tablePrefix . config('railforums.tables.post_reports');
        ConfigService::$tablePostReplies = ConfigService::$tablePrefix . config('railforums.tables.post_replies');
        ConfigService::$tableSearchIndexes = ConfigService::$tablePrefix . config('railforums.tables.search_indexes');

        // author table
        ConfigService::$authorTableName = config('railforums.author_table_name');
        ConfigService::$authorTableIdColumnName = config('railforums.author_table_id_column_name');
        ConfigService::$authorTableDisplayNameColumnName = config('railforums.author_table_display_name_column_name');

        // middleware
        ConfigService::$controllerMiddleware = config('railforums.controller_middleware');

        // No need for decorators yet
        // config()->set(
        //     'resora.decorators.threads',
        //     array_merge(
        //         config()->get('resora.decorators.threads', []),
        //         [ThreadDecorator::class]
        //     )
        // );
    }
}
