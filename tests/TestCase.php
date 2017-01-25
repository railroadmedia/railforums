<?php

namespace Tests;

use Faker\Generator;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @var Generator
     */
    protected $faker;

    protected function setUp()
    {
        parent::setUp();

        $this->loadMigrationsFrom(
            [
                '--database' => 'testbench',
                '--realpath' => realpath(__DIR__ . '/../migrations'),
            ]
        );

        $this->faker = $this->app->make(Generator::class);
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

        $app['db']->connection()->getSchemaBuilder()->create(
            'users',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('display_name');
            }
        );
    }

    /**
     * @return mixed
     */
    public function fakeUser()
    {
        $displayName = $this->faker->userName . rand();

        $this->app['db']->connection()->table('users')->insert(
            ['display_name' => $displayName]
        );

        return [
            'display_name' => $displayName,
            'id' => $this->app['db']->connection()->getPdo()->lastInsertId('id')
        ];
    }
}