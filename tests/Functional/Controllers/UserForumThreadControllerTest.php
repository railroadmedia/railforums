<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Permissions\Exceptions\NotAllowedException;

class UserForumThreadControllerTest extends TestCase
{
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
            '/thread/read/' . $thread['id']
        );

        // assert the session has the success message
        $response->assertSessionHas('success', true);

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
            '/thread/read/' . $thread['id']
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
            '/thread/read/' . $threadId
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
            '/thread/follow/' . $thread['id']
        );

        // assert the session has the success message
        $response->assertSessionHas('success', true);

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
            '/thread/follow/' . $thread['id']
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());

        // assert the thread data was not saved in the db
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

        $response = $this->call(
            'PUT',
            '/thread/follow/' . $threadId
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
            '/thread/unfollow/' . $thread['id']
        );

        // assert the session has the success message
        $response->assertSessionHas('success', true);

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
            '/thread/unfollow/' . $thread['id']
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());

        // assert the data was removed from the db
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

        $response = $this->call(
            'DELETE',
            '/thread/unfollow/' . $threadFollowId
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
            '/thread/store',
            $threadData
        );

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

        // assert the session has the success message
        $response->assertSessionHas('success', true);
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
            '/thread/store',
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
            '/thread/store',
            []
        );

        // assert the session has the error messages
        $response->assertSessionHasErrors(
            ['title', 'first_post_content', 'category_id']
        );
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
            '/thread/update/' . $thread['id'],
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

        // assert the session has the success message
        $response->assertSessionHas('success', true);
    }

    public function test_thread_update_without_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], rand(2, 32767));

        $newTitle = $this->faker->sentence();

        $this->permissionServiceMock->method('canOrThrow')->willThrowException(
            new NotAllowedException('You are not allowed to update-threads')
        );

        $response = $this->call(
            'PATCH',
            '/thread/update/' . $thread['id'],
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
            '/thread/update/' . $thread['id'],
            ['title' => '']
        );

        // assert response status code
        $this->assertEquals(302, $response->getStatusCode());

        // assert validation fail
        $response->assertSessionHasErrors(['title']);
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
            '/thread/update/' . rand(0, 32767),
            ['title' => $newTitle]
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }
}
