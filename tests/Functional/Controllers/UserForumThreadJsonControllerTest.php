<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Permissions\Exceptions\NotAllowedException;

class UserForumThreadJsonControllerTest extends TestCase
{
    const API_PREFIX = '/forums';

    protected function setUp()
    {
        $this->setDefaultConnection('mysql');

        parent::setUp();
    }

    public function test_thread_read_with_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        $this->fakePost($thread['id'], $user->getId());

        $this->permissionServiceMock->method('canOrThrow')->willReturn(true);

        $response = $this->call(
            'PUT',
            self::API_PREFIX . '/thread/read/' . $thread['id']
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $response->assertJsonFragment([
            'thread_id' => $thread['id'],
            'reader_id' => (int) $user->getId()
        ]);

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tableThreadReads,
            [
                'thread_id' => $thread['id'],
                'reader_id' => $user->getId()
            ]
        );
    }

    public function test_thread_read_without_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        $otherUserId = rand(2, 32767);

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $otherUserId);

        $this->fakePost($thread['id'], $otherUserId);

        $this->permissionServiceMock->method('canOrThrow')->willThrowException(
            new NotAllowedException('You are not allowed to read-threads')
        );

        $response = $this->call(
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
                'reader_id' => $user->getId()
            ]
        );
    }

    public function test_thread_read_not_exists()
    {
        $user = $this->fakeCurrentUserCloak();

        $threadId = rand(0, 32767);

        $this->permissionServiceMock->method('canOrThrow')->willReturn(true);

        $response = $this->call(
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
                'reader_id' => $user->getId()
            ]
        );
    }

    public function test_thread_follow_with_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        $this->fakePost($thread['id'], $user->getId());

        $this->permissionServiceMock->method('canOrThrow')->willReturn(true);

        $response = $this->call(
            'PUT',
            self::API_PREFIX . '/thread/follow/' . $thread['id']
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $response->assertJsonFragment([
            'thread_id' => $thread['id'],
            'follower_id' => (int) $user->getId()
        ]);

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tableThreadFollows,
            [
                'thread_id' => $thread['id'],
                'follower_id' => $user->getId()
            ]
        );
    }

    public function test_thread_follow_without_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        $otherUserId = rand(2, 32767);

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $otherUserId);

        $this->fakePost($thread['id'], $otherUserId);

        $this->permissionServiceMock->method('canOrThrow')->willThrowException(
            new NotAllowedException('You are not allowed to follow-threads')
        );

        $response = $this->call(
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
                'follower_id' => $user->getId()
            ]
        );
    }

    public function test_thread_follow_not_exists()
    {
        $user = $this->fakeCurrentUserCloak();
        $threadId = rand(0, 32767);

        $this->permissionServiceMock->method('canOrThrow')->willReturn(true);

        $response = $this->call(
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
                'follower_id' => $user->getId()
            ]
        );
    }

    public function test_thread_unfollow_with_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        $thread = $this->fakeThread(null, $user->getId());

        $dateTime = Carbon::instance($this->faker->dateTime)->toDateTimeString();

        $threadFollow = [
            'thread_id' => $thread['id'],
            'follower_id' => $user->getId(),
            'followed_on' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];

        $this->databaseManager
            ->table(ConfigService::$tableThreadFollows)
            ->insertGetId($threadFollow);

        $this->assertDatabaseHas(
            ConfigService::$tableThreadFollows,
            [
                'thread_id' => $thread['id'],
                'follower_id' => $user->getId()
            ]
        );

        $this->permissionServiceMock->method('canOrThrow')->willReturn(true);

        $response = $this->call(
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
                'follower_id' => $user->getId()
            ]
        );
    }

    public function test_thread_unfollow_without_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        $otherUserId = rand(2, 32767);

        $thread = $this->fakeThread(null, $otherUserId);

        $dateTime = Carbon::instance($this->faker->dateTime)->toDateTimeString();

        $threadFollow = [
            'thread_id' => $thread['id'],
            'follower_id' => $user->getId(),
            'followed_on' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];

        $this->databaseManager
            ->table(ConfigService::$tableThreadFollows)
            ->insertGetId($threadFollow);

        $this->permissionServiceMock->method('canOrThrow')->willThrowException(
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
                'follower_id' => $user->getId()
            ]
        );
    }

    public function test_thread_unfollow_not_exists()
    {
        $this->fakeCurrentUserCloak();

        $threadFollowId = rand(0, 32767);

        $this->permissionServiceMock->method('canOrThrow')->willReturn(true);

        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/thread/unfollow/' . $threadFollowId
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_thread_index_with_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $categoryOne */
        $categoryOne = $this->fakeCategory();

        $threads = [];

        for ($i=0; $i < 20; $i++) {
            /** @var array $thread */
            $thread = $this->fakeThread($categoryOne['id'], $user->getId());

            $threads[$thread['id']] = $thread;
        }

        /** @var array $categoryTwo */
        $categoryTwo = $this->fakeCategory();

        for ($i=0; $i < 10; $i++) {
            /** @var array $thread */
            $thread = $this->fakeThread($categoryTwo['id'], $user->getId());

            $threads[$thread['id']] = $thread;
        }

        $payload = [
            'amount' => 10,
            'page' => 1,
            'category_ids' => [$categoryOne['id']]
        ];

        $this->permissionServiceMock->method('canOrThrow')->willReturn(true);

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/thread/index',
            $payload
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $results = $response->decodeResponseJson();

        // assert reponse entities count is the requested amount
        $this->assertEquals(count($results['threads']), $payload['amount']);

        // assert reponse has threads count for pagination
        $this->assertArrayHasKey('count', $results);

        // assert reponse entities have the requested category
        foreach ($results['threads'] as $thread) {
            $this->assertEquals($thread['category_id'], $categoryOne['id']);
        }
    }

    public function test_thread_index_without_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        $otherUserId = rand(2, 32767);

        $this->fakeThread($category['id'], $otherUserId);

        $payload = [
            'amount' => 10,
            'page' => 1,
            'category_ids' => [$category['id']]
        ];

        $this->permissionServiceMock->method('canOrThrow')->willThrowException(
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
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        $this->permissionServiceMock->method('canOrThrow')->willReturn(true);

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/thread/show/' . $thread['id']
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $this->assertArraySubset(
            [
                'title' => $thread['title'],
                'category_id' => (int) $category['id'],
                'author_id' => (int) $user->getId()
            ],
            $response->decodeResponseJson()
        );
    }

    public function test_thread_show_without_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        $otherUserId = rand(2, 32767);

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $otherUserId);

        $this->permissionServiceMock->method('canOrThrow')->willThrowException(
            new NotAllowedException('You are not allowed to show-threads')
        );

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/thread/show/' . $thread['id']
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_thread_show_with_decorated_data()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        /** @var array $post */
        $post = $this->fakePost($thread['id'], $user->getId());

        $dateTime = Carbon::instance($this->faker->dateTime)->toDateTimeString();

        $threadFollow = [
            'thread_id' => $thread['id'],
            'follower_id' => $user->getId(),
            'followed_on' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];

        $this->databaseManager
            ->table(ConfigService::$tableThreadFollows)
            ->insertGetId($threadFollow);

        $threadRead = [
            'thread_id' => $thread['id'],
            'reader_id' => $user->getId(),
            'read_on' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];

        $this->databaseManager
            ->table(ConfigService::$tableThreadReads)
            ->insertGetId($threadRead);

        $this->permissionServiceMock->method('canOrThrow')->willReturn(true);

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/thread/show/' . $thread['id']
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $this->assertArraySubset(
            [
                'title' => $thread['title'],
                'category_id' => (int) $category['id'],
                'author_id' => (int) $user->getId(),
                'post_count' => 1,
                'last_post_published_on' => $post['published_on'],
                'last_post_id' => $post['id'],
                'last_post_user_id' => $user->getId(),
                'last_post_user_display_name' => $user->getDisplayName(),
                'is_read' => 1,
                'is_followed' => 1
            ],
            $response->decodeResponseJson()
        );
    }

    public function test_thread_show_not_exists()
    {
        $this->permissionServiceMock->method('canOrThrow')->willReturn(true);

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/thread/show/' . rand(0, 32767)
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_thread_store_with_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        $threadData = [
            'title' => $this->faker->sentence(),
            'first_post_content' => $this->faker->paragraph(),
            'category_id' => $category['id'],
        ];

        $this->permissionServiceMock->method('canOrThrow')->willReturn(true);

        $response = $this->call(
            'PUT',
            self::API_PREFIX . '/thread/store',
            $threadData
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $response->assertJsonFragment([
            'title' => $threadData['title'],
            'category_id' => (int) $category['id'],
            'author_id' => (int) $user->getId()
        ]);

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tableThreads,
            [
                'title' => $threadData['title'],
                'category_id' => $category['id'],
                'author_id' => $user->getId()
            ]
        );

        // assert the first post data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tablePosts,
            [
                'content' => $threadData['first_post_content'],
                'author_id' => $user->getId()
            ]
        );
    }

    public function test_thread_store_without_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        $threadData = [
            'title' => $this->faker->sentence(),
            'first_post_content' => $this->faker->paragraph(),
            'category_id' => $category['id'],
        ];

        $this->permissionServiceMock->method('canOrThrow')->willThrowException(
            new NotAllowedException('You are not allowed to create-threads')
        );

        $response = $this->call(
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
                'author_id' => $user->getId()
            ]
        );

        // assert the first post data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tablePosts,
            [
                'content' => $threadData['first_post_content'],
                'author_id' => $user->getId()
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
        $this->assertEquals([
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
            ]
        ], $response->decodeResponseJson()['errors']);
    }

    public function test_thread_update_with_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        $newTitle = $this->faker->sentence();

        $this->permissionServiceMock->method('canOrThrow')->willReturn(true);
        $this->permissionServiceMock
            ->method('columns')
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
                'title' => $newTitle
            ]
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $response->assertJsonFragment([
            'title' => $newTitle,
            'category_id' => (int) $category['id'],
            'author_id' => (int) $user->getId()
        ]);
    }

    public function test_thread_update_without_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        $otherUserId = rand(2, 32767);

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $otherUserId);

        $newTitle = $this->faker->sentence();

        $this->permissionServiceMock->method('canOrThrow')->willThrowException(
            new NotAllowedException('You are not allowed to update-threads')
        );

        $response = $this->call(
            'PATCH',
            self::API_PREFIX . '/thread/update/' . $thread['id'],
            ['title' => $newTitle]
        );

        // assert the thread data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tableThreads,
            [
                'id' => $thread['id'],
                'title' => $newTitle
            ]
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_thread_update_validation_fail()
    {
        $user = $this->fakeCurrentUserCloak();
        $thread = $this->fakeThread(null, $user->getId());

        $this->permissionServiceMock->method('canOrThrow')->willReturn(true);

        $response = $this->call(
            'PATCH',
            self::API_PREFIX . '/thread/update/' . $thread['id'],
            ['title' => '']
        );

        // assert response status code
        $this->assertEquals(422, $response->getStatusCode());

        // assert validation error messages
        $this->assertEquals([
            [
                "source" => "title",
                "detail" => "The title must be at least 1 characters.",
            ]
        ], $response->decodeResponseJson()['errors']);
    }

    public function test_thread_update_not_found()
    {
        $newTitle = $this->faker->sentence();

        $this->permissionServiceMock->method('canOrThrow')->willReturn(true);
        $this->permissionServiceMock
            ->method('columns')
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
        $thread = $this->fakeThread();

        $this->permissionServiceMock->method('canOrThrow')->willReturn(true);

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
                'id' => $thread['id']
            ]
        );
    }

    public function test_thread_delete_without_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        $otherUserId = rand(2, 32767);

        /** @var array $thread */
        $thread = $this->fakeThread(null, $otherUserId);

        $this->permissionServiceMock->method('canOrThrow')->willThrowException(
            new NotAllowedException('You are not allowed to delete-threads')
        );

        $response = $this->call(
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
                'deleted_at' => null
            ]
        );
    }

    public function test_thread_delete_not_found()
    {
        $this->permissionServiceMock->method('canOrThrow')->willReturn(true);

        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/thread/delete/' . rand(0, 32767)
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }
}
