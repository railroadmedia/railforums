<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Entities\PostLike;

class UserForumPostControllerTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    public function test_post_like()
    {
        $user = $this->fakeCurrentUserCloak();

        $thread = $this->fakeThread(null, $user->getId());

        $post = $this->fakePost($thread->getId(), $user->getId());

        $response = $this->call(
            'PUT',
            '/post/like/' . $post->getId()
        );

        // assert the session has the success message
        $response->assertSessionHas('success', true);

        // assert the post like data was saved in the db
        $this->assertDatabaseHas(
            'forum_post_likes',
            [
                'post_id' => $post->getId(),
                'liker_id' => $user->getId()
            ]
        );
    }

    public function test_post_like_not_exists()
    {
        $user = $this->fakeCurrentUserCloak();
        $postId = rand(0, 32767);

        $response = $this->call(
            'PUT',
            '/post/like/' . $postId
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());

        // assert the data was not saved in the db
        $this->assertDatabaseMissing(
            'forum_post_likes',
            [
                'post_id' => $postId,
                'liker_id' => $user->getId()
            ]
        );
    }

    public function test_post_unlike()
    {
        $user = $this->fakeCurrentUserCloak();

        $thread = $this->fakeThread(null, $user->getId());

        $post = $this->fakePost($thread->getId(), $user->getId());

        $postLike = new PostLike();
        $postLike->setPostId($post->getId());
        $postLike->setLikerId($user->getId());
        $postLike->setLikedOn(Carbon::now()->toDateTimeString());
        $postLike->persist();

        $this->assertDatabaseHas(
            'forum_post_likes',
            [
                'post_id' => $post->getId(),
                'liker_id' => $user->getId()
            ]
        );

        $response = $this->call(
            'DELETE',
            '/post/unlike/' . $post->getId()
        );

        // assert the session has the success message
        $response->assertSessionHas('success', true);

        // assert the data was removed from the db
        $this->assertDatabaseMissing(
            'forum_post_likes',
            [
                'post_id' => $post->getId(),
                'liker_id' => $user->getId()
            ]
        );
    }

    public function test_post_store()
    {
        $user = $this->fakeCurrentUserCloak();

        $category = $this->fakeCategory();

        $thread = $this->fakeThread($category->getId(), $user->getId());

        $postData = [
            'content' => $this->faker->sentence(),
            'thread_id' => $thread->getId()
        ];

        $response = $this->call(
            'PUT',
            '/post/store',
            $postData
        );

        // assert the post data was saved in the db
        $this->assertDatabaseHas(
            'forum_posts',
            [
                'content' => $postData['content'],
                'thread_id' => $postData['thread_id']
            ]
        );

        // assert the session has the success message
        $response->assertSessionHas('success', true);
    }

    public function test_post_store_validation_fail()
    {
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

    public function test_post_update()
    {
        $user = $this->fakeCurrentUserCloak();

        $thread = $this->fakeThread(null, $user->getId());

        $post = $this->fakePost($thread->getId(), $user->getId());

        $newContent = $this->faker->sentence();

        $response = $this->call(
            'PATCH',
            '/post/update/' . $post->getId(),
            ['content' => $newContent]
        );

        // assert the post data was saved in the db
        $this->assertDatabaseHas(
            'forum_posts',
            [
                'id' => $post->getId(),
                'content' => $newContent
            ]
        );

        // assert the session has the success message
        $response->assertSessionHas('success', true);
    }

    public function test_post_update_validation_fail()
    {
        $user = $this->fakeCurrentUserCloak();
        $thread = $this->fakeThread(null, $user->getId());
        $post = $this->fakePost($thread->getId(), $user->getId());
        $newContent = $this->faker->sentence();

        $response = $this->call(
            'PATCH',
            '/post/update/' . $post->getId(),
            []
        );

        // assert response status code
        $this->assertEquals(302, $response->getStatusCode());

        // assert validation fail
        $response->assertSessionHasErrors(['content']);

        // assert new content was not saved in db
        $this->assertDatabaseMissing(
            'forum_posts',
            [
                'id' => $post->getId(),
                'content' => $newContent
            ]
        );
    }

    public function test_post_update_not_found()
    {
        $response = $this->call(
            'PATCH',
            '/post/update/' . rand(0, 32767),
            ['content' => $this->faker->sentence()]
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }
}
