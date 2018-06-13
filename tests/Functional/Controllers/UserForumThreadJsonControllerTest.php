<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Entities\ThreadFollow;
use Railroad\Railforums\Services\ConfigService;

class UserForumThreadJsonControllerTest extends TestCase
{
    const API_PREFIX = '/forums';

    protected function setUp()
    {
        $this->setDefaultConnection('mysql');

        parent::setUp();
    }

    // public function test_thread_read()
    // {
    //     $user = $this->fakeCurrentUserCloak();

    //     $thread = $this->fakeThread(null, $user->getId());

    //     // at least one post is required for a thread to be marked as read
    //     $this->fakePost($thread->getId(), $user->getId());

    //     $response = $this->call(
    //         'PUT',
    //         self::API_PREFIX . '/thread/read/' . $thread->getId()
    //     );

    //     // assert response status code
    //     $this->assertEquals(200, $response->getStatusCode());

    //     // assert response data
    //     $response->assertJsonFragment([
    //         'threadId' => $thread->getId(),
    //         'readerId' => $user->getId()
    //     ]);

    //     // assert the thread data was saved in the db
    //     $this->assertDatabaseHas(
    //         'forum_thread_reads',
    //         [
    //             'thread_id' => $thread->getId(),
    //             'reader_id' => $user->getId()
    //         ]
    //     );
    // }

    // public function test_thread_read_not_exists()
    // {
    //     $user = $this->fakeCurrentUserCloak();

    //     $threadId = rand(0, 32767);

    //     $response = $this->call(
    //         'PUT',
    //         self::API_PREFIX . '/thread/read/' . $threadId
    //     );

    //     // assert response status code
    //     $this->assertEquals(404, $response->getStatusCode());

    //     // assert the data was not saved in the db
    //     $this->assertDatabaseMissing(
    //         'forum_thread_reads',
    //         [
    //             'thread_id' => $threadId,
    //             'reader_id' => $user->getId()
    //         ]
    //     );
    // }

    // public function test_thread_follow()
    // {
    //     $user = $this->fakeCurrentUserCloak();

    //     $thread = $this->fakeThread(null, $user->getId());

    //     $response = $this->call(
    //         'PUT',
    //         self::API_PREFIX . '/thread/follow/' . $thread->getId()
    //     );

    //     // assert response status code
    //     $this->assertEquals(204, $response->getStatusCode());

    //     // assert the thread data was saved in the db
    //     $this->assertDatabaseHas(
    //         'forum_thread_follows',
    //         [
    //             'thread_id' => $thread->getId(),
    //             'follower_id' => $user->getId()
    //         ]
    //     );
    // }

    // public function test_thread_follow_not_exists()
    // {
    //     $user = $this->fakeCurrentUserCloak();
    //     $threadId = rand(0, 32767);

    //     $response = $this->call(
    //         'PUT',
    //         self::API_PREFIX . '/thread/follow/' . $threadId
    //     );

    //     // assert response status code
    //     $this->assertEquals(404, $response->getStatusCode());

    //     // assert the data was not saved in the db
    //     $this->assertDatabaseMissing(
    //         'forum_thread_follows',
    //         [
    //             'thread_id' => $threadId,
    //             'follower_id' => $user->getId()
    //         ]
    //     );
    // }

    // public function test_thread_unfollow()
    // {
    //     $user = $this->fakeCurrentUserCloak();

    //     $thread = $this->fakeThread(null, $user->getId());

    //     $threadFollow = new ThreadFollow();
    //     $threadFollow->setThreadId($thread->getId());
    //     $threadFollow->setFollowerId($user->getId());
    //     $threadFollow->setFollowedOn(Carbon::now()->toDateTimeString());
    //     $threadFollow->persist();

    //     $this->assertDatabaseHas(
    //         'forum_thread_follows',
    //         [
    //             'thread_id' => $thread->getId(),
    //             'follower_id' => $user->getId()
    //         ]
    //     );

    //     $response = $this->call(
    //         'DELETE',
    //         self::API_PREFIX . '/thread/unfollow/' . $thread->getId()
    //     );

    //     // assert response status code
    //     $this->assertEquals(204, $response->getStatusCode());

    //     // assert the data was removed from the db
    //     $this->assertDatabaseMissing(
    //         'forum_thread_follows',
    //         [
    //             'thread_id' => $thread->getId(),
    //             'follower_id' => $user->getId()
    //         ]
    //     );
    // }

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

        $this->permissionServiceMock->method('can')->willReturn(true);

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

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        $payload = [
            'amount' => 10,
            'page' => 1,
            'category_ids' => [$category['id']]
        ];

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/thread/index',
            $payload
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_thread_show_with_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        $this->permissionServiceMock->method('can')->willReturn(true);

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

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/thread/show/' . $thread['id']
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
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

        $this->permissionServiceMock->method('can')->willReturn(true);

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/thread/show/' . $thread['id']
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // TODO: add thread read and thread followed above and assert data below

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
                'is_read' => 0,
                'is_followed' => 0
            ],
            $response->decodeResponseJson()
        );
    }

    public function test_thread_show_not_exists()
    {
        $this->permissionServiceMock->method('can')->willReturn(true);

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

        $this->permissionServiceMock->method('can')->willReturn(true);

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

        $response = $this->call(
            'PUT',
            self::API_PREFIX . '/thread/store',
            $threadData
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());

        // assert the thread data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tableThreads,
            [
                'title' => $threadData['title'],
                'category_id' => $category['id'],
                'author_id' => $user->getId()
            ]
        );

        // assert the first post data was saved in the db
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

        $this->permissionServiceMock->method('can')->willReturn(true);

        $newTitle = $this->faker->sentence();

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

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        $newTitle = $this->faker->sentence();

        $response = $this->call(
            'PATCH',
            self::API_PREFIX . '/thread/update/' . $thread['id'],
            ['title' => $newTitle]
        );

        // assert the thread data was saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tableThreads,
            [
                'id' => $thread['id'],
                'title' => $newTitle
            ]
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_thread_update_validation_fail()
    {
        $user = $this->fakeCurrentUserCloak();
        $thread = $this->fakeThread(null, $user->getId());

        $this->permissionServiceMock->method('can')->willReturn(true);

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
        $this->permissionServiceMock->method('can')->willReturn(true);

        $response = $this->call(
            'PATCH',
            self::API_PREFIX . '/thread/update/' . rand(0, 32767),
            ['title' => $this->faker->sentence()]
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_thread_delete()
    {
        $thread = $this->fakeThread();

        $this->permissionServiceMock->method('can')->willReturn(true);

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
        $thread = $this->fakeThread();

        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/thread/delete/' . $thread['id']
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());

        // assert the thread data was not soft deleted or deleted from db
        $this->assertDatabaseHas(
            'forum_threads',
            [
                'id' => $thread['id'],
                'deleted_at' => null
            ]
        );
    }

    public function test_thread_delete_not_found()
    {
        $this->permissionServiceMock->method('can')->willReturn(true);

        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/thread/delete/' . rand(0, 32767)
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }
}
