<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Services\ConfigService;

class UserForumPostControllerTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    public function test_post_like_with_permission()
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
            'PUT',
            '/post/like/' . $post['id']
        );

        // assert the session has the success message
        $response->assertSessionHas('success', true);

        // assert the post like data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tablePostLikes,
            [
                'post_id' => $post['id'],
                'liker_id' => $user->getId()
            ]
        );
    }

    public function test_post_like_without_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        /** @var array $post */
        $post = $this->fakePost($thread['id'], $user->getId());

        $response = $this->call(
            'PUT',
            '/post/like/' . $post['id']
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());

        // assert the data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tablePostLikes,
            [
                'post_id' => $post['id'],
                'liker_id' => $user->getId()
            ]
        );
    }

    public function test_post_like_not_exists()
    {
        $user = $this->fakeCurrentUserCloak();
        $postId = rand(0, 32767);

        $this->permissionServiceMock->method('can')->willReturn(true);

        $response = $this->call(
            'PUT',
            '/post/like/' . $postId
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());

        // assert the data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tablePostLikes,
            [
                'post_id' => $postId,
                'liker_id' => $user->getId()
            ]
        );
    }

    public function test_post_unlike_with_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        /** @var array $post */
        $post = $this->fakePost($thread['id'], $user->getId());

        $dateTime = Carbon::instance($this->faker->dateTime)->toDateTimeString();

        $postLike = [
            'post_id' => $post['id'],
            'liker_id' => $user->getId(),
            'liked_on' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];

        $this->databaseManager
            ->table(ConfigService::$tablePostLikes)
            ->insertGetId($postLike);

        $this->assertDatabaseHas(
            ConfigService::$tablePostLikes,
            [
                'post_id' => $post['id'],
                'liker_id' => $user->getId()
            ]
        );

        $this->permissionServiceMock->method('can')->willReturn(true);

        $response = $this->call(
            'DELETE',
            '/post/unlike/' . $post['id']
        );

        // assert the session has the success message
        $response->assertSessionHas('success', true);

        // assert the data was removed from the db
        $this->assertDatabaseMissing(
            ConfigService::$tablePostLikes,
            [
                'post_id' => $post['id'],
                'liker_id' => $user->getId()
            ]
        );
    }

    public function test_post_unlike_without_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        /** @var array $post */
        $post = $this->fakePost($thread['id'], $user->getId());

        $dateTime = Carbon::instance($this->faker->dateTime)->toDateTimeString();

        $postLike = [
            'post_id' => $post['id'],
            'liker_id' => $user->getId(),
            'liked_on' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];

        $this->databaseManager
            ->table(ConfigService::$tablePostLikes)
            ->insertGetId($postLike);

        $response = $this->call(
            'DELETE',
            '/post/unlike/' . $post['id']
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());

        // assert the data was not removed from the db
        $this->assertDatabaseHas(
            ConfigService::$tablePostLikes,
            [
                'post_id' => $post['id'],
                'liker_id' => $user->getId()
            ]
        );
    }

    public function test_post_store_with_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        $postData = [
            'content' => $this->faker->sentence(),
            'thread_id' => $thread['id']
        ];

        $this->permissionServiceMock->method('can')->willReturn(true);

        $response = $this->call(
            'PUT',
            '/post/store',
            $postData
        );

        // assert the post data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tablePosts,
            [
                'content' => $postData['content'],
                'thread_id' => $thread['id']
            ]
        );

        // assert the session has the success message
        $response->assertSessionHas('success', true);
    }

    public function test_post_store_without_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        $postData = [
            'content' => $this->faker->sentence(),
            'thread_id' => $thread['id']
        ];

        $response = $this->call(
            'PUT',
            '/post/store',
            $postData
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());

        // assert the post data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tablePosts,
            [
                'content' => $postData['content'],
                'thread_id' => $thread['id']
            ]
        );
    }

    public function test_post_store_validation_fail()
    {
        $this->fakeCurrentUserCloak();

        $this->permissionServiceMock->method('can')->willReturn(true);

        $response = $this->call(
            'PUT',
            '/post/store',
            []
        );

        // assert the session has the error messages
        $response->assertSessionHasErrors(
            ['content', 'thread_id']
        );
    }

    public function test_post_update_with_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        /** @var array $post */
        $post = $this->fakePost($thread['id'], $user->getId());

        $newContent = $this->faker->sentence();

        $this->permissionServiceMock->method('can')->willReturn(true);
        $this->permissionServiceMock
            ->method('columns')
            ->willReturn(['content' => $newContent]);

        $response = $this->call(
            'PATCH',
            '/post/update/' . $post['id'],
            ['content' => $newContent]
        );

        // assert the post data was saved in the db
        $this->assertDatabaseHas(
            'forum_posts',
            [
                'id' => $post['id'],
                'content' => $newContent
            ]
        );

        // assert the session has the success message
        $response->assertSessionHas('success', true);
    }

    public function test_post_update_without_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        /** @var array $post */
        $post = $this->fakePost($thread['id'], $user->getId());

        $newContent = $this->faker->sentence();

        $response = $this->call(
            'PATCH',
            '/post/update/' . $post['id'],
            ['content' => $newContent]
        );

        // assert the post data was not saved in the db
        $this->assertDatabaseMissing(
            'forum_posts',
            [
                'id' => $post['id'],
                'content' => $newContent
            ]
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_post_update_validation_fail()
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
            'PATCH',
            '/post/update/' . $post['id'],
            []
        );

        // assert response status code
        $this->assertEquals(302, $response->getStatusCode());

        // assert validation fail
        $response->assertSessionHasErrors(['content']);
    }

    public function test_post_update_not_found()
    {
        $this->fakeCurrentUserCloak();

        $newContent = $this->faker->sentence();

        $this->permissionServiceMock->method('can')->willReturn(true);
        $this->permissionServiceMock
            ->method('columns')
            ->willReturn(['content' => $newContent]);

        $response = $this->call(
            'PATCH',
            '/post/update/' . rand(0, 32767),
            ['content' => $newContent]
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }
}
