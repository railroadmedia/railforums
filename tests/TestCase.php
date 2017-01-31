<?php

namespace Tests;

use Faker\Generator;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Railroad\Railforums\ForumServiceProvider;
use Railroad\Railmap\RailmapServiceProvider;

class TestCase extends BaseTestCase
{
    /**
     * @var Generator
     */
    protected $faker;

    protected function setUp()
    {
        parent::setUp();

        $this->artisan('migrate', []);

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

        $app['config']->set(
            'railforums.html_purifier_settings',
            [
                'encoding' => 'UTF-8',
                'finalize' => true,
                'settings' => [
                    'default' => [
                        'HTML.Doctype' => 'XHTML 1.0 Strict',
                        'HTML.Allowed' => 'div,b,strong,i,em,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]',
                        'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align',
                        'AutoFormat.AutoParagraph' => true,
                        'AutoFormat.RemoveEmpty' => true,
                    ],
                ],
            ]
        );

        $app['config']->set(
            'railforums.user_data_mapper_class',
            \Railroad\Railforums\DataMappers\UserCloakDataMapper::class
        );

        $app['db']->connection()->getSchemaBuilder()->create(
            'users',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('display_name');
                $table->string('avatar_url')->nullable();
                $table->string('access_type');
            }
        );

        $app->register(RailmapServiceProvider::class);
        $app->register(ForumServiceProvider::class);
    }

    /**
     * @return mixed
     */
    public function fakeUser()
    {
        $displayName = $this->faker->userName . rand();

        $this->app['db']->connection()->table('users')->insert(
            ['display_name' => $displayName, 'access_type' => 'user',]
        );

        return [
            'display_name' => $displayName,
            'access_type' => 'user',
            'id' => $this->app['db']->connection()->getPdo()->lastInsertId('id')
        ];
    }
}