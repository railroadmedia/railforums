<?php

namespace Tests;

class UserForumPostControllerTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
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

        // assert the thread data was saved in the db
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

    public function test_thread_store_validation_fail()
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

    public function test_thread_update()
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

        // assert the thread data was saved in the db
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

    public function test_thread_update_validation_fail()
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

    public function test_thread_update_not_found()
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
