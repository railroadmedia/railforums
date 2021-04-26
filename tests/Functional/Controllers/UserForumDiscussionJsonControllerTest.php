<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Permissions\Exceptions\NotAllowedException;
use Railroad\Railforums\Services\ConfigService;

class UserForumDiscussionJsonControllerTest extends TestCase
{
    const API_PREFIX = '/forums';

    protected function setUp()
    {
        $this->setDefaultConnection('testbench');

        parent::setUp();
    }

    public function test_discussions_index_with_permission_and_pagination()
    {
        $discussions = [];
        $user = $this->fakeUser();

        for ($i = 0; $i < 20; $i++) {
            /** @var array $category */
            $category = $this->fakeCategory();

            $discussions[] = $category;

            $thread1 = $this->fakeThread($category['id'], $this->fakeUser()['id']);
            $this->fakeThread($category['id'], $this->fakeUser()['id']);

            $this->fakePost(
                $thread1['id'],
                $this->fakeUser()['id'],
                'post 1',
                Carbon::now()
                    ->subDays(10)
                    ->toDateTimeString()
            );
            $this->fakePost(
                $thread1['id'],
                $this->fakeUser()['id'],
                'post 1',
                Carbon::now()
                    ->subDays(1)
                    ->toDateTimeString()
            );
            $latestPost[$category['id']] =
                $this->fakePost(
                    $thread1['id'],
                    $user['id'],
                    'post 2',
                    Carbon::now()
                        ->toDateTimeString()
                );
        }

        $discussions =
            collect($discussions)
                ->sortBy('created_at')
                ->toArray();

        $payload = [
            'amount' => 5,
            'page' => 1,
        ];

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);
        $response = $this->call(
            'GET',
            self::API_PREFIX . '/discussions/index',
            $payload
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $results = $response->decodeResponseJson();

        $this->assertEquals(count($results['results']), $payload['amount']);

        // assert reponse entities have the requested category
        foreach ($results['results'] as $index => $category) {
            $this->assertEquals($category['id'], $discussions[$index]['id']);
            $this->assertEquals($category['title'], $discussions[$index]['title']);
            $this->assertEquals($category['latest_post']['id'], $latestPost[$category['id']]['id']);
        }
    }

    public function test_discussions_index_with_permission_without_pagination()
    {
        $discussions = [];
        $user = $this->fakeUser();

        for ($i = 0; $i < 20; $i++) {
            /** @var array $category */
            $category = $this->fakeCategory();

            $discussions[] = $category;

            $thread1 = $this->fakeThread($category['id'], rand(1, 7));
            $this->fakeThread($category['id'], rand(10, 15));

            $this->fakePost(
                $thread1['id'],
                rand(2, 5),
                'post 1',
                Carbon::now()
                    ->subDays(10)
                    ->toDateTimeString()
            );
            $this->fakePost(
                $thread1['id'],
                rand(2, 5),
                'post 1',
                Carbon::now()
                    ->subDays(1)
                    ->toDateTimeString()
            );
            $latestPost[$category['id']] =
                $this->fakePost(
                    $thread1['id'],
                    $user['id'],
                    'post 2',
                    Carbon::now()
                        ->toDateTimeString()
                );
        }

        $discussions =
            collect($discussions)
                ->sortBy('created_at')
                ->toArray();

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);
        $response = $this->call(
            'GET',
            self::API_PREFIX . '/discussions/index'
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $results = $response->decodeResponseJson();

        $this->assertEquals(count($results['results']), count($discussions));

        // assert response entities have the requested category
        foreach ($results['results'] as $index => $category) {
            $this->assertEquals($category['id'], $discussions[$index]['id']);
            $this->assertEquals($category['title'], $discussions[$index]['title']);
            $this->assertEquals($category['latest_post']['id'], $latestPost[$category['id']]['id']);
        }
    }

    public function test_discussions_index_without_permission()
    {
        $payload = [
            'amount' => 10,
            'page' => 1,
        ];

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to index-discussions')
            );

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/discussions/index',
            $payload
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_discussion_show_with_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response =
            $this->actingAs($user)
                ->call(
                    'GET',
                    self::API_PREFIX . '/discussions/show/' . $category['id']
                );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $this->assertArraySubset(
            [
                'title' => $category['title'],
                'description' => $category['description']
            ],
            $response->decodeResponseJson()
        );
    }

    public function test_discussion_show_without_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to show-discussions')
            );

        $response =
            $this->actingAs($user)
                ->call(
                    'GET',
                    self::API_PREFIX . '/discussions/show/' . $category['id']
                );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_discussion_show_with_decorated_data()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();
        $category2 = $this->fakeCategory();

        /** @var array $thread */
        $thread1 = $this->fakeThread($category['id'], $user['id']);
        $thread = $this->fakeThread($category2['id'], $user['id']);

        /** @var array $post */
        for ($i = 0; $i < 15; $i++) {
            $post = $this->fakePost($thread['id'], $user['id']);
        }

        for ($i = 0; $i < 5; $i++) {
            $post = $this->fakePost($thread1['id'], $user['id']);
        }

        $dateTime =
            Carbon::instance($this->faker->dateTime)
                ->toDateTimeString();

        $threadFollow = [
            'thread_id' => $thread['id'],
            'follower_id' => $user['id'],
            'followed_on' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];

        $this->databaseManager->table(ConfigService::$tableThreadFollows)
            ->insertGetId($threadFollow);

        $threadRead = [
            'thread_id' => $thread['id'],
            'reader_id' => $user['id'],
            'read_on' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];

        $this->databaseManager->table(ConfigService::$tableThreadReads)
            ->insertGetId($threadRead);

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response =
            $this->actingAs($user)
                ->call(
                    'GET',
                    self::API_PREFIX . '/discussions/show/' . $category['id']
                );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

    }

    public function test_discussion_show_not_exists()
    {
        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/discussion/show/' . rand(0, 32767)
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_discussion_store_with_permission()
    {
        $user = $this->fakeUser();

        $categoryData = [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph()
        ];

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response =
            $this->actingAs($user)
                ->call(
                    'PUT',
                    self::API_PREFIX .'/discussions/store',
                    $categoryData
                );

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tableCategories,
            [
                'title' => $categoryData['title'],
                'description' => $categoryData['description']
            ]
        );
    }

    public function test_discussion_store_without_permission()
    {
        $user = $this->fakeUser();

        $categoryData = [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph()
        ];

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to create-discussions')
            );

        $response =
            $this->actingAs($user)
                ->call(
                    'PUT',
                    self::API_PREFIX . '/discussions/store',
                    $categoryData
                );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());

        // assert the thread data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tableCategories,
            [
                'title' => $categoryData['title'],
                'desccription' => $categoryData['description'],
            ]
        );
    }

    public function test_discussion_store_validation_fail()
    {
        $response = $this->call(
            'PUT',
            self::API_PREFIX .'/discussions/store',
            []
        );

        // assert response status code
        $this->assertEquals(422, $response->getStatusCode());

        // assert response validation error messages
        $this->assertEquals([
            [
                "source" => "title",
                "detail" => "The title field is required.",
            ]
        ], $response->decodeResponseJson()['errors']);
    }

    public function test_discussion_update_with_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();


        $newTitle = $this->faker->sentence();

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);
        $this->permissionServiceMock->method('columns')
            ->willReturn(['title' => $newTitle]);

        $response = $this->call(
            'PATCH',
            self::API_PREFIX . '/discussions/update/' . $category['id'],
            ['title' => $newTitle]
        );

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tableCategories,
            [
                'id' => $category['id'],
                'title' => $newTitle,
            ]
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $this->assertEquals($newTitle, $response->decodeResponseJson('title'));
        $this->assertEquals($category['id'], $response->decodeResponseJson('id'));
    }

    public function test_discussion_update_without_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        $newTitle = $this->faker->sentence();

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to update-discussions')
            );

        $response = $this->actingAs($user)->call(
            'PATCH',
            self::API_PREFIX . '/discussions/update/' . $category['id'],
            ['title' => $newTitle]
        );

        // assert the thread data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tableCategories,
            [
                'id' => $category['id'],
                'title' => $newTitle,
            ]
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_discussion_update_validation_fail()
    {
        $user = $this->fakeUser();

        $category = $this->fakeCategory();

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->actingAs($user)->call(
            'PATCH',
            self::API_PREFIX . '/discussions/update/' . $category['id'],
            ['title' => '']
        );

        // assert response status code
        $this->assertEquals(422, $response->getStatusCode());

        // assert validation error messages
        $this->assertEquals(
            [
                [
                    "source" => "title",
                    "detail" => "The title must be at least 1 characters.",
                ],
            ],
            $response->decodeResponseJson()['errors']
        );
    }

    public function test_discussion_update_not_found()
    {
        $newTitle = $this->faker->sentence();

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);
        $this->permissionServiceMock->method('columns')
            ->willReturn(['title' => $newTitle]);

        $response = $this->call(
            'PATCH',
            self::API_PREFIX . '/discussions/update/' . rand(0, 32767),
            ['title' => $newTitle]
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_discussion_delete()
    {
        $discussion = $this->fakeCategory();

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/discussions/delete/' . $discussion['id']
        );

        // assert response status code
        $this->assertEquals(204, $response->getStatusCode());

        // assert the category data was marked as soft deleted
        $this->assertSoftDeleted(
            'forum_categories',
            [
                'id' => $discussion['id'],
            ]
        );
    }

    public function test_discussion_delete_without_permission()
    {
        $user = $this->fakeUser();

     $discussion = $this->fakeCategory();

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to delete-discussions')
            );

        $response = $this->actingAs($user)->call(
            'DELETE',
            self::API_PREFIX . '/discussions/delete/' . $discussion['id']
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());

        // assert the thread data was not soft deleted or deleted from db
        $this->assertDatabaseHas(
            ConfigService::$tableCategories,
            [
                'id' => $discussion['id'],
                'deleted_at' => null,
            ]
        );
    }

    public function test_discussion_delete_not_found()
    {
        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/discussions/delete/' . rand(0, 32767)
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

}
