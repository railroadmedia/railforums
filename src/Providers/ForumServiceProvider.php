<?php

namespace Railroad\Railforums\Providers;

use Illuminate\Support\ServiceProvider;
use Railroad\Railforums\Services\ConfigService;

class ForumServiceProvider extends ServiceProvider
{
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

        // $this->commands([
        //     CreateSearchIndexes::class
        // ]);
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

        // author table
        ConfigService::$authorTableName = config('railforums.author_table_name');
        ConfigService::$authorTableIdColumnName = config('railforums.author_table_id_column_name');
        ConfigService::$authorTableDisplayNameColumnName = config('railforums.author_table_display_name_column_name');
    }
}
