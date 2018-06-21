<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Permissions\Services\ConfigService as PermissionsConfigService;

class ThreadUpdateColumnsTest extends TestCase
{
    const API_PREFIX = '/forums';

    protected function setUp()
    {
        $this->setDefaultConnection('mysql');

        $this->enablePermissionServiceMocking = false;

        parent::setUp();
    }

    public function test_thread_update_with_administrator()
    {
        $user = $this->fakeCurrentUserCloak();

        // \Illuminate\Support\Facades\Auth::loginUsingId($user->getId()); // Class '\App\User' not found
        // $this->be($user); // requires instance of Illuminate\Contracts\Auth\Authenticatable as the parameter

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        $dateTime = Carbon::instance($this->faker->dateTime)->toDateTimeString();

        $ability = 'update-threads';

        $administratorRole = 'administrator';
        $role = [
            'user_id'    => $user->getId(),
            'role'       => $administratorRole,
            'created_at' => $dateTime,
            'updated_at' => $dateTime
        ];
        $administratorAbility = [
            'user_id'    => $user->getId(),
            'ability'    => $ability,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];

        $this->databaseManager->table(PermissionsConfigService::$tableUserRoles)
            ->insert($role);
        $this->databaseManager->table(PermissionsConfigService::$tableUserAbilities)
            ->insert($administratorAbility);

        $newTitle = $this->faker->sentence();
        $newSlug = strtolower(implode('-', $this->faker->words(5)));

        $response = $this->call(
            'PATCH',
            self::API_PREFIX . '/thread/update/' . $thread['id'],
            [
                'title' => $newTitle,
                'slug' => $newSlug
            ]
        );

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tableThreads,
            [
                'id' => $thread['id'],
                'title' => $newTitle,
                'slug' => $newSlug
            ]
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());
    }
}
