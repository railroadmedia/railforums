<?php

namespace Tests;

class UserForumPostJsonControllerTest extends TestCase
{
    const API_PREFIX = '/forums';

    protected function setUp()
    {
        parent::setUp();
    }

    public function test_post_index()
    {
        $user = $this->fakeCurrentUserCloak();

        $category = $this->fakeCategory();

        $threadOne = $this->fakeThread($category->getId(), $user->getId());

        $posts = [];

        for ($i=0; $i < 20; $i++) { 
            $post = $this->fakePost($threadOne->getId(), $user->getId());

            $posts[$post->getId()] = $post;
        }

        $threadTwo = $this->fakeThread($category->getId(), $user->getId());

        for ($i=0; $i < 10; $i++) { 
            $post = $this->fakeThread($threadTwo->getId(), $user->getId());

            $posts[$post->getId()] = $post;
        }

        $payload = [
            'amount' => 10,
            'page' => 1,
            'thread_id' => $threadOne->getId()
        ];

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/post/index',
            $payload
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $results = $response->decodeResponseJson();

        // assert reponse entities count is the requested amount
        $this->assertEquals(count($results), $payload['amount']);

        // assert reponse entities have the requested category
        foreach ($results as $post) {
            $this->assertEquals($post['threadId'], $payload['thread_id']);
        }
    }

    public function test_post_show()
    {
        $user = $this->fakeCurrentUserCloak();

        $thread = $this->fakeThread(null, $user->getId());

        $post = $this->fakePost($thread->getId(), $user->getId());

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/post/show/' . $post->getId()
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $response->assertJsonFragment([
            'content' => $post->getContent()
        ]);
    }

    public function test_post_show_not_exists()
    {
        $response = $this->call(
            'GET',
            self::API_PREFIX . '/post/show/' . rand(0, 32767)
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
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
            self::API_PREFIX . '/post/store',
            $postData
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            'forum_posts',
            [
                'content' => $postData['content'],
                'thread_id' => $postData['thread_id']
            ]
        );

        // assert response data
        $response->assertJsonFragment([
            'content' => $postData['content'],
            'threadId' => $postData['thread_id']
        ]);
    }

    public function test_thread_store_validation_fail()
    {
        $response = $this->call(
            'PUT',
            self::API_PREFIX . '/post/store',
            []
        );

        // assert response status code
        $this->assertEquals(422, $response->getStatusCode());

        // assert response validation error messages
        $this->assertEquals([
            [
                "source" => "content",
                "detail" => "The content field is required.",
            ],
            [
                "source" => "thread_id",
                "detail" => "The thread id field is required.",
            ]
        ], $response->decodeResponseJson()['errors']);
    }
    
    public function test_post_update()
    {
        $user = $this->fakeCurrentUserCloak();

        $thread = $this->fakeThread(null, $user->getId());

        $post = $this->fakePost($thread->getId(), $user->getId());

        $newContent = $this->faker->sentence();

        $response = $this->call(
            'PATCH',
            self::API_PREFIX . '/post/update/' . $post->getId(),
            ['content' => $newContent]
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert the thread data was saved in the db
        $this->assertDatabaseHas(
            'forum_posts',
            [
                'id' => $post->getId(),
                'content' => $newContent
            ]
        );

        // assert response data
        $response->assertJsonFragment([
            'content' => $newContent,
            'id' => $post->getId()
        ]);
    }

    public function test_post_update_validation_fail()
    {
        $user = $this->fakeCurrentUserCloak();
        $thread = $this->fakeThread(null, $user->getId());
        $post = $this->fakePost($thread->getId(), $user->getId());
        $newContent = $this->faker->sentence();

        $response = $this->call(
            'PATCH',
            self::API_PREFIX . '/post/update/' . $post->getId(),
            []
        );

        // assert response status code
        $this->assertEquals(422, $response->getStatusCode());

        // assert response validation error messages
        $this->assertEquals([
            [
                "source" => "content",
                "detail" => "The content field is required.",
            ]
        ], $response->decodeResponseJson()['errors']);

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
            self::API_PREFIX . '/post/update/' . rand(0, 32767),
            ['content' => $this->faker->sentence()]
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_post_delete()
    {
        $user = $this->fakeCurrentUserCloak();
        $category = $this->fakeCategory();
        $thread = $this->fakeThread($category->getId(), $user->getId());
        $post = $this->fakePost($thread->getId(), $user->getId());

        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/post/delete/' . $post->getId()
        );

        // assert response status code
        $this->assertEquals(204, $response->getStatusCode());

        // assert the thread data was marked as soft deleted
        $this->assertSoftDeleted(
            'forum_posts',
            [
                'id' => $post->getId()
            ]
        );
    }

    public function test_post_delete_not_found()
    {
        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/post/delete/' . rand(0, 32767)
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }
}
