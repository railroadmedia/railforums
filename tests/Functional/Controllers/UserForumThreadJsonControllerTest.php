<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Entities\ThreadFollow;

class UserForumThreadJsonControllerTest extends TestCase
{
    const API_PREFIX = '/forums';

    protected function setUp()
    {
        parent::setUp();
    }

    public function test_thread_read()
    {
        $user = $this->fakeCurrentUserCloak();

        $thread = $this->fakeThread(null, $user->getId());

        // at least one post is required for a thread to be marked as read
        $this->fakePost($thread->getId(), $user->getId());

        $response = $this->call(
            'PUT',
            self::API_PREFIX . '/thread/read/' . $thread->getId()
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $response->assertJsonFragment([
            'threadId' => $thread->getId(),
            'readerId' => $user->getId()
        ]);

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            'forum_thread_reads',
            [
                'thread_id' => $thread->getId(),
                'reader_id' => $user->getId()
            ]
        );
    }

    public function test_thread_read_not_exists()
    {
        $user = $this->fakeCurrentUserCloak();

        $threadId = rand(0, 32767);

        $response = $this->call(
            'PUT',
            self::API_PREFIX . '/thread/read/' . $threadId
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());

        // assert the data was not saved in the db
        $this->assertDatabaseMissing(
            'forum_thread_reads',
            [
                'thread_id' => $threadId,
                'reader_id' => $user->getId()
            ]
        );
    }

    public function test_thread_follow()
    {
        $user = $this->fakeCurrentUserCloak();

        $thread = $this->fakeThread(null, $user->getId());

        $response = $this->call(
            'PUT',
            self::API_PREFIX . '/thread/follow/' . $thread->getId()
        );

        // assert response status code
        $this->assertEquals(204, $response->getStatusCode());

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            'forum_thread_follows',
            [
                'thread_id' => $thread->getId(),
                'follower_id' => $user->getId()
            ]
        );
    }

    public function test_thread_follow_not_exists()
    {
        $user = $this->fakeCurrentUserCloak();
        $threadId = rand(0, 32767);

        $response = $this->call(
            'PUT',
            self::API_PREFIX . '/thread/follow/' . $threadId
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());

        // assert the data was not saved in the db
        $this->assertDatabaseMissing(
            'forum_thread_follows',
            [
                'thread_id' => $threadId,
                'follower_id' => $user->getId()
            ]
        );
    }

    public function test_thread_unfollow()
    {
        $user = $this->fakeCurrentUserCloak();

        $thread = $this->fakeThread(null, $user->getId());

        $threadFollow = new ThreadFollow();
        $threadFollow->setThreadId($thread->getId());
        $threadFollow->setFollowerId($user->getId());
        $threadFollow->setFollowedOn(Carbon::now()->toDateTimeString());
        $threadFollow->persist();

        $this->assertDatabaseHas(
            'forum_thread_follows',
            [
                'thread_id' => $thread->getId(),
                'follower_id' => $user->getId()
            ]
        );

        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/thread/unfollow/' . $thread->getId()
        );

        // assert response status code
        $this->assertEquals(204, $response->getStatusCode());

        // assert the data was removed from the db
        $this->assertDatabaseMissing(
            'forum_thread_follows',
            [
                'thread_id' => $thread->getId(),
                'follower_id' => $user->getId()
            ]
        );
    }

    public function test_thread_index()
    {
        $user = $this->fakeCurrentUserCloak();

        $categoryOne = $this->fakeCategory();

        $threads = [];

        for ($i=0; $i < 20; $i++) { 
            $thread = $this->fakeThread($categoryOne->getId(), $user->getId());

            $threads[$thread->getId()] = $thread;
        }

        $categoryTwo = $this->fakeCategory();

        for ($i=0; $i < 10; $i++) { 
            $thread = $this->fakeThread($categoryTwo->getId(), $user->getId());

            $threads[$thread->getId()] = $thread;
        }

        $payload = [
            'amount' => 10,
            'page' => 1,
            'category_id' => $categoryOne->getId()
        ];

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/thread/index',
            $payload
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $results = $response->decodeResponseJson();

        // assert reponse entities count is the requested amount
        $this->assertEquals(count($results), $payload['amount']);

        // assert reponse entities have the requested category
        foreach ($results as $thread) {
            $this->assertEquals($thread['categoryId'], $payload['category_id']);
        }
    }

    public function test_thread_show()
    {
        $user = $this->fakeCurrentUserCloak();

        $category = $this->fakeCategory();

        $thread = $this->fakeThread($category->getId(), $user->getId());

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/thread/show/' . $thread->getId()
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $response->assertJsonFragment([
            'title' => $thread->getTitle(),
            'categoryId' => (int) $category->getId(),
            'authorId' => (int) $user->getId()
        ]);
    }

    public function test_thread_show_not_exists()
    {
        $response = $this->call(
            'GET',
            self::API_PREFIX . '/thread/show/' . rand(0, 32767)
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_thread_store()
    {
        $user = $this->fakeCurrentUserCloak();

        $category = $this->fakeCategory();

        $threadData = [
            'title' => $this->faker->sentence(),
            'first_post_content' => $this->faker->paragraph(),
            'category_id' => $category->getId(),
        ];

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
            'categoryId' => (int) $category->getId(),
            'authorId' => (int) $user->getId()
        ]);

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            'forum_threads',
            [
                'title' => $threadData['title'],
                'category_id' => $category->getId(),
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

    public function test_thread_update()
    {
        $user = $this->fakeCurrentUserCloak();

        $category = $this->fakeCategory();

        $thread = $this->fakeThread($category->getId(), $user->getId());

        $newTitle = $this->faker->sentence();

        $response = $this->call(
            'PATCH',
            self::API_PREFIX . '/thread/update/' . $thread->getId(),
            ['title' => $newTitle]
        );

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            'forum_threads',
            [
                'id' => $thread->getId(),
                'title' => $newTitle
            ]
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $response->assertJsonFragment([
            'title' => $newTitle,
            'categoryId' => (int) $category->getId(),
            'authorId' => (int) $user->getId()
        ]);
    }

    public function test_thread_update_validation_fail()
    {
        $user = $this->fakeCurrentUserCloak();
        $thread = $this->fakeThread(null, $user->getId());

        $response = $this->call(
            'PATCH',
            self::API_PREFIX . '/thread/update/' . $thread->getId(),
            []
        );

        // assert response status code
        $this->assertEquals(422, $response->getStatusCode());

        // assert validation error messages
        $this->assertEquals([
            [
                "source" => "title",
                "detail" => "The title field is required.",
            ]
        ], $response->decodeResponseJson()['errors']);
    }

    public function test_thread_update_not_found()
    {
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

        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/thread/delete/' . $thread->getId()
        );

        // assert response status code
        $this->assertEquals(204, $response->getStatusCode());

        // assert the thread data was marked as soft deleted
        $this->assertSoftDeleted(
            'forum_threads',
            [
                'id' => $thread->getId()
            ]
        );
    }

    public function test_thread_delete_not_found()
    {
        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/thread/delete/' . rand(0, 32767)
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }
}
