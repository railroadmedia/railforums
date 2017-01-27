<?php namespace Railroad\Railforums;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Railroad\Railforums\EventListeners\EntityEventListener;
use Railroad\Railmap\Events\EntityCreated;
use Railroad\Railmap\Events\EntitySaved;

class ForumServiceProvider extends ServiceProvider
{
    protected $listen = [
        EntityCreated::class => [
            EntityEventListener::class . '@onCreated',
        ],
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
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
        //
    }
}
