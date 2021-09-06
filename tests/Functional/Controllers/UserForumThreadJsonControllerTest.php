<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Permissions\Exceptions\NotAllowedException;
use Railroad\Railforums\Services\ConfigService;

class UserForumThreadJsonControllerTest extends TestCase
{
    const API_PREFIX = '/forums';

    protected function setUp()
    {
        $this->setDefaultConnection('testbench');

        parent::setUp();
    }

    public function test_thread_read_with_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user['id']);

        $this->fakePost($thread['id'], $user['id']);

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response =
            $this->actingAs($user)
                ->call(
                    'PUT',
                    self::API_PREFIX . '/thread/read/' . $thread['id']
                );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $this->assertEquals($user['id'], $response->decodeResponseJson('reader_id'));
        $this->assertEquals($thread['id'], $response->decodeResponseJson('thread_id'));

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tableThreadReads,
            [
                'thread_id' => $thread['id'],
                'reader_id' => $user['id'],
            ]
        );
    }

    public function test_thread_read_without_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        $otherUserId = rand(2, 32767);

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $otherUserId);

        $this->fakePost($thread['id'], $otherUserId);

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to read-threads')
            );

        $response = $this->actingAs($user)->call(
            'PUT',
            self::API_PREFIX . '/thread/read/' . $thread['id']
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());

        // assert the thread data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tableThreadReads,
            [
                'thread_id' => $thread['id'],
                'reader_id' => $user['id'],
            ]
        );
    }

    public function test_thread_read_not_exists()
    {
        $user = $this->fakeUser();

        $threadId = rand(0, 32767);

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->actingAs($user)->call(
            'PUT',
            self::API_PREFIX . '/thread/read/' . $threadId
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());

        // assert the data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tableThreadReads,
            [
                'thread_id' => $threadId,
                'reader_id' => $user['id'],
            ]
        );
    }

    public function test_thread_follow_with_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user['id']);

        $this->fakePost($thread['id'], $user['id']);

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response =
            $this->actingAs($user)
                ->call(
                    'PUT',
                    self::API_PREFIX . '/thread/follow/' . $thread['id']
                );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $this->assertArraySubset(
            [
                'thread_id' => $thread['id'],
                'follower_id' => $user['id'],
            ],
            $response->decodeResponseJson()
        );

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tableThreadFollows,
            [
                'thread_id' => $thread['id'],
                'follower_id' => $user['id'],
            ]
        );
    }

    public function test_thread_follow_without_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        $otherUserId = rand(2, 32767);

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $otherUserId);

        $this->fakePost($thread['id'], $otherUserId);

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to follow-threads')
            );

        $response = $this->actingAs($user)->call(
            'PUT',
            self::API_PREFIX . '/thread/follow/' . $thread['id']
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());

        // assert the thread data was saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tableThreadFollows,
            [
                'thread_id' => $thread['id'],
                'follower_id' => $user['id'],
            ]
        );
    }

    public function test_thread_follow_not_exists()
    {
        $user = $this->fakeUser();
        $threadId = rand(0, 32767);

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->actingAs($user)->call(
            'PUT',
            self::API_PREFIX . '/thread/follow/' . $threadId
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());

        // assert the data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tableThreadFollows,
            [
                'thread_id' => $threadId,
                'follower_id' => $user['id'],
            ]
        );
    }

    public function test_thread_unfollow_with_permission()
    {
        $user = $this->fakeUser();

        $thread = $this->fakeThread(null, $user['id']);

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

        $this->assertDatabaseHas(
            ConfigService::$tableThreadFollows,
            [
                'thread_id' => $thread['id'],
                'follower_id' => $user['id'],
            ]
        );

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response =
            $this->actingAs($user)
                ->call(
                    'DELETE',
                    self::API_PREFIX . '/thread/unfollow/' . $thread['id']
                );

        // assert response status code
        $this->assertEquals(204, $response->getStatusCode());

        // assert the data was removed from the db
        $this->assertDatabaseMissing(
            ConfigService::$tableThreadFollows,
            [
                'thread_id' => $thread['id'],
                'follower_id' => $user['id'],
            ]
        );
    }

    public function test_thread_unfollow_without_permission()
    {
        $user = $this->fakeUser();

        $otherUserId = rand(2, 32767);

        $thread = $this->fakeThread(null, $otherUserId);

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

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to follow-threads')
            );

        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/thread/unfollow/' . $thread['id']
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());

        // assert the data was not removed from the db
        $this->assertDatabaseHas(
            ConfigService::$tableThreadFollows,
            [
                'thread_id' => $thread['id'],
                'follower_id' => $user['id'],
            ]
        );
    }

    public function test_thread_unfollow_not_exists()
    {
        $this->fakeUser();

        $threadFollowId = rand(0, 32767);

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/thread/unfollow/' . $threadFollowId
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_thread_index_with_permission()
    {
        $user = $this->fakeUser();

        /** @var array $categoryOne */
        $categoryOne = $this->fakeCategory();

        $threads = [];

        for ($i = 0; $i < 20; $i++) {
            /** @var array $thread */
            $thread = $this->fakeThread($categoryOne['id'], $user['id']);

            $threads[$thread['id']] = $thread;
        }

        /** @var array $categoryTwo */
        $categoryTwo = $this->fakeCategory();

        for ($i = 0; $i < 10; $i++) {
            /** @var array $thread */
            $thread = $this->fakeThread($categoryTwo['id'], $user['id']);

            $threads[$thread['id']] = $thread;
        }

        $payload = [
            'amount' => 10,
            'page' => 1,
            'category_id' => $categoryOne['id'],
        ];

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/thread/index',
            $payload
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $results = $response->decodeResponseJson();

        // assert reponse entities count is the requested amount
        $this->assertEquals(count($results['results']), $payload['amount']);

        // assert reponse has threads count for pagination
        $this->assertArrayHasKey('total_results', $results);

        // assert response entities have the requested category
        foreach ($results['results'] as $thread) {
            $this->assertEquals($thread['category_id'], $categoryOne['id']);
        }
    }

    public function test_thread_index_without_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        $otherUserId = rand(2, 32767);

        $this->fakeThread($category['id'], $otherUserId);

        $payload = [
            'amount' => 10,
            'page' => 1,
            'category_ids' => [$category['id']],
        ];

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to index-threads')
            );

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/thread/index',
            $payload
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_thread_show_with_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user['id']);

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->actingAs($user)->call(
            'GET',
            self::API_PREFIX . '/thread/show/' . $thread['id']
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $this->assertArraySubset(
            [
                'title' => $thread['title'],
                'category_id' => (int)$category['id'],
                'author_id' => $user['id'],
            ],
            $response->decodeResponseJson()
        );
    }

    public function test_thread_show_with_decorated_data()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user['id']);

        /** @var array $post */
        $post = $this->fakePost($thread['id'], $user['id']);

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
                    self::API_PREFIX . '/thread/show/' . $thread['id']
                );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $this->assertArraySubset(
            [
                'title' => $thread['title'],
                'category_id' => (int)$category['id'],
                'author_id' => (int)$user['id'],
                'post_count' => 1,
                'last_post_published_on' => $post['published_on'],
                'last_post_id' => $post['id'],
                'last_post_user_id' => $user['id'],
                'is_read' => 1,
                'is_followed' => 1,
            ],
            $response->decodeResponseJson()
        );
    }

    public function test_thread_show_not_exists()
    {
        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/thread/show/' . rand(0, 32767)
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_thread_store_with_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        $threadData = [
            'title' => $this->faker->sentence(),
            'first_post_content' => $this->faker->paragraph(),
            'category_id' => $category['id'],
        ];

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response =
            $this->actingAs($user)
                ->call(
                    'PUT',
                    self::API_PREFIX . '/thread/store',
                    $threadData
                );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $this->assertEquals($threadData['title'], $response->decodeResponseJson('title'));
        $this->assertEquals($category['id'], $response->decodeResponseJson('category_id'));
        $this->assertEquals($user['id'], $response->decodeResponseJson('author_id'));

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tableThreads,
            [
                'title' => $threadData['title'],
                'category_id' => $category['id'],
                'author_id' => $user['id'],
            ]
        );

        // assert the first post data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tablePosts,
            [
                'content' => $threadData['first_post_content'],
                'author_id' => $user['id'],
            ]
        );
    }

    public function test_thread_store_without_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        $threadData = [
            'title' => $this->faker->sentence(),
            'first_post_content' => $this->faker->paragraph(),
            'category_id' => $category['id'],
        ];

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to create-threads')
            );

        $response = $this->actingAs($user)->call(
            'PUT',
            self::API_PREFIX . '/thread/store',
            $threadData
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());

        // assert the thread data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tableThreads,
            [
                'title' => $threadData['title'],
                'category_id' => $category['id'],
                'author_id' => $user['id'],
            ]
        );

        // assert the first post data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tablePosts,
            [
                'content' => $threadData['first_post_content'],
                'author_id' => $user['id'],
            ]
        );
    }

    public function test_thread_store_validation_fail()
    {
        $response = $this->call(
            'PUT',
            self::API_PREFIX . '/thread/store',
            []
        );

        // assert response status code
        $this->assertEquals(422, $response->getStatusCode());

        // assert response validation error messages
        $this->assertEquals(
            [
                [
                    "source" => "title",
                    "detail" => "The title field is required.",
                ],
                [
                    "source" => "first_post_content",
                    "detail" => "The first post content field is required.",
                ],
                [
                    "source" => "category_id",
                    "detail" => "The category id field is required.",
                ],
            ],
            $response->decodeResponseJson()['errors']
        );
    }

    public function test_thread_update_with_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user['id']);

        $newTitle = $this->faker->sentence();

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);
        $this->permissionServiceMock->method('columns')
            ->willReturn(['title' => $newTitle]);

        $response = $this->call(
            'PATCH',
            self::API_PREFIX . '/thread/update/' . $thread['id'],
            ['title' => $newTitle]
        );

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tableThreads,
            [
                'id' => $thread['id'],
                'title' => $newTitle,
            ]
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $this->assertEquals($newTitle, $response->decodeResponseJson('title'));
        $this->assertEquals($category['id'], $response->decodeResponseJson('category_id'));
        $this->assertEquals($user['id'], $response->decodeResponseJson('author_id'));
    }

    public function test_thread_update_without_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        $otherUserId = $this->fakeUser()['id'];

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $otherUserId);

        $newTitle = $this->faker->sentence();

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to update-threads')
            );

        $response = $this->actingAs($user)->call(
            'PATCH',
            self::API_PREFIX . '/thread/update/' . $thread['id'],
            ['title' => $newTitle]
        );

        // assert the thread data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tableThreads,
            [
                'id' => $thread['id'],
                'title' => $newTitle,
            ]
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_thread_update_validation_fail()
    {
        $user = $this->fakeUser();
        $thread = $this->fakeThread(null, $user['id']);

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->actingAs($user)->call(
            'PATCH',
            self::API_PREFIX . '/thread/update/' . $thread['id'],
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

    public function test_thread_update_not_found()
    {
        $newTitle = $this->faker->sentence();

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);
        $this->permissionServiceMock->method('columns')
            ->willReturn(['title' => $newTitle]);

        $response = $this->call(
            'PATCH',
            self::API_PREFIX . '/thread/update/' . rand(0, 32767),
            ['title' => $newTitle]
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_thread_delete()
    {
        $author = $this->fakeUser();
        $thread = $this->fakeThread(rand(1,3), $author['id']);

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/thread/delete/' . $thread['id']
        );

        // assert response status code
        $this->assertEquals(204, $response->getStatusCode());

        // assert the thread data was marked as soft deleted
        $this->assertSoftDeleted(
            'forum_threads',
            [
                'id' => $thread['id'],
            ]
        );
    }

    public function test_thread_delete_without_permission()
    {
        $user = $this->fakeUser();

        $otherUserId = $this->fakeUser()['id'];

        /** @var array $thread */
        $thread = $this->fakeThread(null, $otherUserId);

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to delete-threads')
            );

        $response = $this->actingAs($user)->call(
            'DELETE',
            self::API_PREFIX . '/thread/delete/' . $thread['id']
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());

        // assert the thread data was not soft deleted or deleted from db
        $this->assertDatabaseHas(
            ConfigService::$tableThreads,
            [
                'id' => $thread['id'],
                'deleted_at' => null,
            ]
        );
    }

    public function test_thread_delete_not_found()
    {
        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/thread/delete/' . rand(0, 32767)
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_latest_threads_with_permission()
    {
        $user = $this->fakeUser();

        /** @var array $categoryOne */
        $categoryOne = $this->fakeCategory();

        $threads = [];

        //old threads
        for ($i = 0; $i < 20; $i++) {
            /** @var array $thread */
            $thread = $this->fakeThread($categoryOne['id'], $user['id']);
            $this->fakePost($thread['id'], $user['id'], null, Carbon::now()->subYears($i+1)->toDateTimeString());
        }

        /** @var array $categoryTwo */
        $categoryTwo = $this->fakeCategory();

        for ($i = 0; $i < 10; $i++) {
            /** @var array $thread */
            $thread = $this->fakeThread($categoryTwo['id'], $user['id']);
            $this->fakePost($thread['id'], $user['id'], null, Carbon::now()->subDays($i)->toDateTimeString());

            $threads[$i] = $thread;
        }

        $payload = [
            'amount' => 10,
            'page' => 1
        ];

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->actingAs($user)->call(
            'GET',
            self::API_PREFIX .'/api/thread/latest',
            $payload
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $results = $response->decodeResponseJson();

        // assert reponse entities count is the requested amount
        $this->assertEquals(count($results['results']), $payload['amount']);

        // assert reponse has threads count for pagination
        $this->assertArrayHasKey('total_results', $results);

        // assert response entities have the requested category
        foreach ($results['results'] as $index=>$thread) {
            $this->assertEquals($thread['id'], $threads[$index]['id']);
        }
    }

    public function test_thread_mine_threads_with_permission()
    {
        $user = $this->fakeUser();
        $otherUser = $this->fakeUser();

        /** @var array $categoryOne */
        $categoryOne = $this->fakeCategory();

        $threads = [];

        for ($i = 0; $i < 10; $i++) {
            /** @var array $thread */
            $thread = $this->fakeThread($categoryOne['id'], $otherUser['id']);
        }

        for ($i = 0; $i < 10; $i++) {
            /** @var array $thread */
            $thread = $this->fakeThread($categoryOne['id'], $user['id']);
        }

        /** @var array $categoryTwo */
        $categoryTwo = $this->fakeCategory();

        for ($i = 0; $i < 10; $i++) {
            /** @var array $thread */
            $thread = $this->fakeThread($categoryTwo['id'], $user['id']);

            $threads[$thread['id']] = $thread;
        }

        $payload = [
            'amount' => 10,
            'page' => 1,
            'category_id' => $categoryOne['id'],
            'sort' => 'mine'
        ];

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->actingAs($user)->call(
            'GET',
            self::API_PREFIX . '/thread/index',
            $payload
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $results = $response->decodeResponseJson();

        // assert reponse entities count is the requested amount
        $this->assertEquals(count($results['results']), $payload['amount']);

        // assert reponse has threads count for pagination
        $this->assertArrayHasKey('total_results', $results);

        // assert response entities are my threads
        foreach ($results['results'] as $thread) {
            $this->assertEquals($thread['author_id'], $user['id']);
            $this->assertEquals($thread['category_id'], $payload['category_id']);
        }
    }

    public function test_thread_oldest_threads_with_permission()
    {
        $user = $this->fakeUser();
        $otherUser = $this->fakeUser();

        /** @var array $categoryOne */
        $categoryOne = $this->fakeCategory();

        $threads = [];

        for ($i = 0; $i < 10; $i++) {
            /** @var array $thread */
            $thread = $this->fakeThread($categoryOne['id'], $otherUser['id']);
            $this->fakePost($thread['id'], $user['id'], null, Carbon::now()->subYears($i+1)->toDateTimeString());
            $threads[] = $thread;
        }

        $payload = [
            'amount' => 10,
            'page' => 1,
            'category_id' => $categoryOne['id'],
            'sort' => 'last_post_published_on'
        ];

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->actingAs($user)->call(
            'GET',
            self::API_PREFIX . '/thread/index',
            $payload
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $results = $response->decodeResponseJson();

        // assert reponse entities count is the requested amount
        $this->assertEquals(count($results['results']), $payload['amount']);

        // assert reponse has threads count for pagination
        $this->assertArrayHasKey('total_results', $results);

        // assert response entities are my threads
        foreach ($results['results'] as $index=>$thread) {
            $this->assertEquals($thread['id'], array_reverse($threads)[$index]['id']);
            $this->assertEquals($thread['category_id'], $payload['category_id']);
        }
    }

    public function test_thread_filter_mine_posts_with_permission()
    {
        $user = $this->fakeUser();
        $otherUser = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user['id']);

        for ($i = 1; $i <= 30; $i++) {
           $this->fakePost($thread['id'], $this->faker->randomElement([$user['id'], $otherUser['id']]));
        }

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->actingAs($user)->call(
            'GET',
            self::API_PREFIX . '/thread/show/' . $thread['id'],[
                'sort' => 'mine'
            ]
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $results = $response->decodeResponseJson();

        // assert response data
        foreach ($results['posts'] as $index=>$post) {
            $this->assertEquals($post['author_id'], $user['id']);
        }
    }

    public function test_thread_sort_latest_posts_with_permission()
    {
        $user = $this->fakeUser();
        $otherUser = $this->fakeUser();

        /** @var array $categoryOne */
        $categoryOne = $this->fakeCategory();

        $posts = [];

        for ($i = 0; $i < 10; $i++) {
            /** @var array $thread */
            $thread = $this->fakeThread($categoryOne['id'], $otherUser['id']);
            $post = $this->fakePost($thread['id'], $user['id'], null, Carbon::now()->subYears($i+1)->toDateTimeString());
            $posts[] = $post;
        }

        $payload = [
            'amount' => 10,
            'page' => 1,
            'sort' => '-published_on'
        ];

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->actingAs($user)->call(
            'GET',
            self::API_PREFIX . '/thread/show/' . $thread['id'],$payload
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $results = $response->decodeResponseJson();

        // assert response entities are my threads
        foreach ($results['posts'] as $index=>$post) {
            $publishedOnFormatted =  Carbon::parse($post['published_on'])
                    ->timezone('Europe/Bucharest')
                    ->format('M j, Y') .
                ' AT ' .
                Carbon::parse($post['published_on'])
                    ->timezone('Europe/Bucharest')
                    ->format('g:i A');

            $this->assertEquals($post['id'], array_reverse($posts)[$index]['id']);
            $this->assertEquals($post['published_on_formatted'],  $publishedOnFormatted);
        }
    }
}
