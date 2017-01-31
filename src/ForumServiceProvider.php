<?php namespace Railroad\Railforums;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\EventListeners\EntityEventListener;
use Railroad\Railmap\Events\EntitySaved;

class ForumServiceProvider extends ServiceProvider
{
    protected $listen = [
        EntitySaved::class => [
            EntityEventListener::class . '@onSaved',
        ],
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__ . '/../migrations');

        $this->publishes(
            [
                __DIR__ . '/../config/railforums.php' => config_path('railforums.php'),
            ]
        );
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
