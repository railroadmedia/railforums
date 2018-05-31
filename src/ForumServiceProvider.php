<?php namespace Railroad\Railforums;

use Illuminate\Support\ServiceProvider;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\EventListeners\EntityEventListener;
use Railroad\Railmap\Events\EntityDestroyed;
use Railroad\Railmap\Events\EntitySaved;

class ForumServiceProvider extends ServiceProvider
{
    protected $listen = [
        EntitySaved::class => [
            EntityEventListener::class . '@onSaved',
        ],
        EntityDestroyed::class => [
            EntityEventListener::class . '@onDestroyed',
        ],
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(
            [
                __DIR__ . '/../config/railforums.php' => config_path('railforums.php'),
            ]
        );

        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');

        if (config('railforums.data_mode') == 'host') {
            $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            UserCloakDataMapper::class,
            function ($app) {
                $className = config('railforums.user_data_mapper_class');

                return new $className();
            }
        );
    }
}
