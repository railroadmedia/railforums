<?php

namespace Tests;

use Carbon\Carbon;
use Exception;
use Faker\Generator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railforums\ForumServiceProvider;
use Railroad\Railmap\IdentityMap\IdentityMap;
use Railroad\Railmap\RailmapServiceProvider;

class TestCase extends BaseTestCase
{
    /**
     * @var Generator
     */
    protected $faker;

    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * @var UserCloakDataMapper
     */
    protected $userCloakDataMapper;

    protected function setUp()
    {
        parent::setUp();

        $this->artisan('migrate:refresh', []);
        $this->createUsersTable();
        $this->artisan('cache:clear', []);

        $this->faker = $this->app->make(Generator::class);
        $this->databaseManager = $this->app->make(DatabaseManager::class);
        $this->userCloakDataMapper = $this->app->make(UserCloakDataMapper::class);

        IdentityMap::empty();

        Carbon::setTestNow(Carbon::now());
    }

    protected function createUsersTable()
    {
        $this->app['db']->connection()->getSchemaBuilder()->create(
            'users',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('display_name');
                $table->string('label');
                $table->string('permission_level');
                $table->string('avatar_url')->nullable();
            }
        );
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set(
            'database.connections.testbench',
            [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]
        );
        $app['config']->set(
            'railforums.author_table_name',
            'users'
        );
        $app['config']->set(
            'railforums.author_table_id_column_name',
            'id'
        );
        $app['config']->set(
            'railforums.author_table_display_name_column_name',
            'display_name'
        );

        $app['config']->set(
            'railforums.html_purifier_settings',
            [
                'encoding' => 'UTF-8',
                'finalize' => true,
                'settings' => [
                    'default' => [
                    ],
                ],
            ]
        );

        $app['config']->set(
            'railforums.user_data_mapper_class',
            \Railroad\Railforums\DataMappers\UserCloakDataMapper::class
        );

        $app->register(RailmapServiceProvider::class);
        $app->register(ForumServiceProvider::class);
    }

    /**
     * @return UserCloak
     */
    public function fakeUserCloak()
    {
        return $this->userCloakDataMapper->fake();
    }

    /**
     * @param string $permissionLevel
     * @return UserCloak
     */
    public function fakeCurrentUserCloak($permissionLevel = UserCloak::PERMISSION_LEVEL_USER)
    {
        $userCloak = $this->userCloakDataMapper->fake($permissionLevel);

        $this->userCloakDataMapper->setCurrent($userCloak);

        return $userCloak;
    }

    /**
     * @param UserCloak $userCloak
     */
    public function setAuthenticatedUserCloak(UserCloak $userCloak)
    {
        $this->userCloakDataMapper->setCurrent($userCloak);
    }

    /**
     * We don't want to use mockery so this is a reimplementation of the mockery version.
     *
     * @param  array|string $events
     * @return $this
     *
     * @throws \Exception
     */
    public function expectsEvents($events)
    {
        $events = is_array($events) ? $events : func_get_args();

        $mock = $this->getMockBuilder(Dispatcher::class)
            ->setMethods(['fire', 'dispatch'])
            ->getMockForAbstractClass();

        $mock->method('fire')->willReturnCallback(
            function ($called) {
                $this->firedEvents[] = $called;
            }
        );

        $mock->method('dispatch')->willReturnCallback(
            function ($called) {
                $this->firedEvents[] = $called;
            }
        );

        $this->app->instance('events', $mock);

        $this->beforeApplicationDestroyed(
            function () use ($events) {
                $fired = $this->getFiredEvents($events);

                if ($eventsNotFired = array_diff($events, $fired)) {
                    throw new Exception(
                        'These expected events were not fired: [' . implode(', ', $eventsNotFired) . ']'
                    );
                }
            }
        );

        return $this;
    }
}