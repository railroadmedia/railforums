<?php

namespace Tests;

use Carbon\Carbon;
use Exception;
use Faker\Generator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PDO;
use PHPUnit\Framework\MockObject\MockObject;
use Railroad\Permissions\Providers\PermissionsServiceProvider;
use Railroad\Permissions\Services\PermissionService;
use Railroad\Railforums\Contracts\UserProviderInterface;
use Railroad\Railforums\Providers\ForumServiceProvider;
use Railroad\Railforums\Repositories\PostRepository;
use Railroad\Railforums\Repositories\ThreadRepository;
use Railroad\Railforums\Services\ConfigService;
use Tests\Fixtures\UserProvider;
use Tests\Resources\Models\User;

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

    /**
     * @var string
     */
    protected $defaultConnection;

    /**
     * @var MockObject
     */
    protected $permissionServiceMock;

    protected $enablePermissionServiceMocking = true;

    protected function setUp()
    {
        if (!$this->getDefaultConnection()) {
            $this->setDefaultConnection('testbench');
        }

        parent::setUp();
        $this->faker = $this->app->make(Generator::class);
        $this->databaseManager = $this->app->make(DatabaseManager::class);

        $this->artisan('migrate:fresh', []);
        $this->createUsersTable();
        $this->artisan('cache:clear', []);

        if ($this->enablePermissionServiceMocking) {

            $this->permissionServiceMock =
                $this->getMockBuilder(PermissionService::class)
                    ->disableOriginalConstructor()
                    ->getMock();

            $this->app->instance(PermissionService::class, $this->permissionServiceMock);

        } else {
            $this->app->register(PermissionsServiceProvider::class);
        }

        Carbon::setTestNow(Carbon::now());

        $userProvider = new UserProvider();

        $this->app->instance(UserProviderInterface::class, $userProvider);
    }

    protected function createUsersTable()
    {
        $this->app['db']->connection()
            ->getSchemaBuilder()
            ->create(
                config('railforums.author_table_name'),
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('display_name');
                    $table->string('label');
                    $table->string('permission_level');
                    $table->string('avatar_url')
                        ->nullable();
                    $table->dateTime('created_at');
                }
            );
    }

    /**
     * Define environment setup.
     *
     * @param Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {

        $defaultConfig = require(__DIR__ . '/../config/railforums.php');

        foreach ($defaultConfig as $key => $value) {
            $app['config']->set('railforums.' . $key, $value);
        }

        $app['config']->set(
            'database.default',
            config('railforums.connection_mask_prefix') . $this->getDefaultConnection()
        );
        $app['config']->set(
            'railforums.database_connection_name',
            config('railforums.connection_mask_prefix') . $this->getDefaultConnection()
        );
        $app['config']->set(
            'database.connections.' . config('railforums.connection_mask_prefix') . 'testbench',
            [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]
        );
        $app['config']->set(
            'database.connections.' . config('railforums.connection_mask_prefix') . 'mysql',
            [
                'driver' => 'mysql',
                'host' => 'mysql',
                'port' => env('MYSQL_PORT', '3306'),
                'database' => env('MYSQL_DB', 'railformus_tests'),
                'username' => 'root',
                'password' => 'root',
                'charset' => 'utf8',
                'collation' => 'utf8_general_ci',
                'prefix' => '',
                'options' => [
                    PDO::ATTR_PERSISTENT => true,
                ],
            ]
        );
        $app['config']->set(
            'railforums.author_database_connection',
            config('railforums.connection_mask_prefix') . $this->getDefaultConnection()
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
            'railforums.author_table_avatar_column_name',
            'avatar_url'
        );

        $app['config']->set(
            'railforums.html_purifier_settings',
            [
                'encoding' => 'UTF-8',
                'finalize' => true,
                'settings' => [
                    'default' => [],
                ],
            ]
        );

        $app['config']->set(
            'railforums.user_data_mapper_class',
            UserCloakDataMapper::class
        );
        $app['config']->set('railforums.controller_middleware', []);
        // $app->register(RailmapServiceProvider::class);
        // $app->register(ForumServiceProvider::class);

        $app->register(ForumServiceProvider::class);
    }

    protected function fakeCategory()
    {
        $category = [
            'title' => $this->faker->sentence(20),
            'slug' => strtolower(implode('-', $this->faker->words(5))),
            'description' => $this->faker->sentence(20),
            'weight' => $this->faker->numberBetween(),
            'brand' => config('railforums.brand')
        ];

        $categoryId =
            $this->databaseManager->table(ConfigService::$tableCategories)
                ->insertGetId($category);

        $category['id'] = $categoryId;

        return $category;
    }

    protected function fakeThread($categoryId = null, $authorId = null, $postCount = null)
    {
        $thread = [
            'category_id' => $categoryId ?? $this->faker->randomNumber(),
            'author_id' => $authorId ?? $this->faker->randomNumber(),
            'title' => $this->faker->sentence(20),
            'slug' => strtolower(implode('-', $this->faker->words(5))),
            'state' => ThreadRepository::STATE_PUBLISHED,
            'post_count' => $postCount ?? $this->faker->randomNumber(),
            'published_on' => Carbon::instance($this->faker->dateTime)
                ->toDateTimeString(),
        ];

        $threadId =
            $this->databaseManager->table(ConfigService::$tableThreads)
                ->insertGetId($thread);

        $thread['id'] = $threadId;

        return $thread;
    }

    protected function fakePost($threadId = null, $authorId = null, $content = null,  $updatedAt = null)
    {
        $post = [
            'thread_id' => $threadId ?? $this->faker->randomNumber(),
            'author_id' => $authorId ?? $this->faker->randomNumber(),
            'prompting_post_id' => $this->faker->randomNumber(),
            'content' => $content ?? $this->faker->sentence(20),
            'state' => PostRepository::STATE_PUBLISHED,
            'updated_at' => $updatedAt ?? Carbon::instance($this->faker->dateTime)
                    ->toDateTimeString(),
            'published_on' => Carbon::instance($this->faker->dateTime)
                ->toDateTimeString(),
        ];

        $postId =
            $this->databaseManager->table(ConfigService::$tablePosts)
                ->insertGetId($post);

        $post['id'] = $postId;

        return $post;
    }

    protected function fakeSignature($userId = null)
    {
         $signature = [
            'signature' => $this->faker->sentence(20),
            'user_id' => $userId ?? $this->faker->randomNumber(),
            'brand' => config('railforums.brand')
        ];

        $signatureId =
            $this->databaseManager->table(ConfigService::$tableUserSignatures)
                ->insertGetId($signature);

        $signature['id'] = $signatureId;

        return $signature;
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
     * @param array|string $events
     * @return $this
     *
     * @throws Exception
     */
    public function expectsEvents($events)
    {
        $events = is_array($events) ? $events : func_get_args();

        $mock =
            $this->getMockBuilder(Dispatcher::class)
                ->setMethods(['fire', 'dispatch'])
                ->getMockForAbstractClass();

        $mock->method('fire')
            ->willReturnCallback(
                function ($called) {
                    $this->firedEvents[] = $called;
                }
            );

        $mock->method('dispatch')
            ->willReturnCallback(
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

    /**
     * Get database default connection name
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return $this->defaultConnection;
    }

    /**
     * Set database default connection name
     *
     * @param string $name
     * @return TestCase
     */
    public function setDefaultConnection($name)
    {
        $this->defaultConnection = $name;

        return $this;
    }

    /**
     * Set the currently logged in user for the application.
     *
     * @param Authenticatable $user
     * @param string|null $driver
     * @return $this
     */
    public function actingAs($user = null, $driver = null)
    {
        if (!$user) {
            $user = $this->fakeUser();
        }

        parent::actingAs($user);

        request()->setUserResolver(
            function () use ($user) {
                return User::query()
                    ->find($user['id']);
            }
        );

        return $this;
    }

    /**
     * @return int
     */
    public function fakeUser()
    {
        $userId =
            $this->databaseManager->connection()
                ->query()
                ->from('users')
                ->insertGetId(
                    [
                        'display_name' => $this->faker->word,
                        'label' => $this->faker->word,
                        'permission_level' => $this->faker->word,
                        'created_at' => Carbon::now(),
                    ]
                );

        $user =
            User::query()
                ->find($userId);

        return $user;
    }
}
