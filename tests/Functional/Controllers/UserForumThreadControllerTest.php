<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Entities\ThreadFollow;

class UserForumThreadControllerTest extends TestCase
{
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
            '/thread/read/' . $thread->getId()
        );

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            'forum_thread_reads',
            [
                'thread_id' => $thread->getId(),
                'reader_id' => $user->getId()
            ]
        );

        // assert the session has the success message
        $response->assertSessionHas('success', true);
    }

    public function test_thread_read_not_exists()
    {
        $user = $this->fakeCurrentUserCloak();

        $threadId = rand(0, 32767);

        $response = $this->call(
            'PUT',
            '/thread/read/' . $threadId
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
            '/thread/follow/' . $thread->getId()
        );

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            'forum_thread_follows',
            [
                'thread_id' => $thread->getId(),
                'follower_id' => $user->getId()
            ]
        );

        // assert the session has the success message
        $response->assertSessionHas('success', true);
    }

    public function test_thread_follow_not_exists()
    {
        $user = $this->fakeCurrentUserCloak();
        $threadId = rand(0, 32767);

        $response = $this->call(
            'PUT',
            '/thread/follow/' . $threadId
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
            '/thread/unfollow/' . $thread->getId()
        );

        // assert the session has the success message
        $response->assertSessionHas('success', true);

        // assert the data was removed from the db
        $this->assertDatabaseMissing(
            'forum_thread_follows',
            [
                'thread_id' => $thread->getId(),
                'follower_id' => $user->getId()
            ]
        );
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
            '/thread/store',
            $threadData
        );

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            'forum_threads',
            [
                'title' => $threadData['title'],
                'category_id' => $category->getId(),
                'author_id' => $user->getId()
            ]
        );

        // assert the session has the success message
        $response->assertSessionHas('success', true);
    }

    public function test_thread_store_validation_fail()
    {
        $response = $this->call(
            'PUT',
            '/thread/store',
            []
        );

        // assert the session has the error messages
        $response->assertSessionHasErrors(
            ['title', 'first_post_content', 'category_id']
        );
    }

    public function test_thread_update()
    {
        $user = $this->fakeCurrentUserCloak();

        $thread = $this->fakeThread(null, $user->getId());

        $newTitle = $this->faker->sentence();

        $response = $this->call(
            'PATCH',
            '/thread/update/' . $thread->getId(),
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

        // assert the session has the success message
        $response->assertSessionHas('success', true);
    }

    public function test_thread_update_validation_fail()
    {
        $user = $this->fakeCurrentUserCloak();
        $thread = $this->fakeThread(null, $user->getId());

        $response = $this->call(
            'PATCH',
            '/thread/update/' . $thread->getId(),
            []
        );

        // assert response status code
        $this->assertEquals(302, $response->getStatusCode());

        // assert validation fail
        $response->assertSessionHasErrors(['title']);
    }

    public function test_thread_update_not_found()
    {
        $response = $this->call(
            'PATCH',
            '/thread/update/' . rand(0, 32767),
            ['title' => $this->faker->sentence()]
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }
}
