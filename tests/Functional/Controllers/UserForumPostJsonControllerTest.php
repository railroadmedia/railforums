<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Entities\PostLike;
use Railroad\Railforums\Entities\PostReply;
use Railroad\Railforums\DataMappers\PostDataMapper;
use Railroad\Railmap\IdentityMap\IdentityMap;

class UserForumPostJsonControllerTest extends TestCase
{
    const API_PREFIX = '/forums';

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
            self::API_PREFIX . '/post/like/' . $post->getId()
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $response->assertJsonFragment([
            'likerId' => $user->getId(),
            'postId' => $post->getId()
        ]);

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
            self::API_PREFIX . '/post/unlike/' . $post->getId()
        );

        // assert response status code
        $this->assertEquals(204, $response->getStatusCode());

        // assert the data was removed from the db
        $this->assertDatabaseMissing(
            'forum_post_likes',
            [
                'post_id' => $post->getId(),
                'liker_id' => $user->getId()
            ]
        );
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
        $this->assertEquals(count($results['posts']), $payload['amount']);

        // assert reponse has threads count for pagination
        $this->assertArrayHasKey('count', $results);

        // assert reponse entities have the requested category
        foreach ($results['posts'] as $post) {
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

    public function test_post_show_with_replies()
    {
        $user = $this->fakeCurrentUserCloak();

        $thread = $this->fakeThread(null, $user->getId());

        $postsMap = [];

        $postOne = $this->fakePost($thread->getId(), $user->getId());
        $postsMap[$postOne->getId()] = $postOne;

        $postTwo = $this->fakePost($thread->getId(), $user->getId());
        $postsMap[$postTwo->getId()] = $postTwo;

        $postThree = $this->fakePost($thread->getId(), $user->getId());
        $postsMap[$postThree->getId()] = $postThree;

        $postFour = $this->fakePost($thread->getId(), $user->getId());
        $postsMap[$postFour->getId()] = $postFour;

        $repliesMap = [];

        $postReplyOneFour = new PostReply();
        $postReplyOneFour->setParentPostId($postOne->getId());
        $postReplyOneFour->setChildPostId($postFour->getId());
        $postReplyOneFour->persist();

        $repliesMap[$postReplyOneFour->getId()] = $postReplyOneFour;

        $postReplyTwoFour = new PostReply();
        $postReplyTwoFour->setParentPostId($postTwo->getId());
        $postReplyTwoFour->setChildPostId($postFour->getId());
        $postReplyTwoFour->persist();

        $repliesMap[$postReplyTwoFour->getId()] = $postReplyTwoFour;

        $postReplyThreeFour = new PostReply();
        $postReplyThreeFour->setParentPostId($postThree->getId());
        $postReplyThreeFour->setChildPostId($postFour->getId());
        $postReplyThreeFour->persist();

        // ^^ postFour is set to quote/reply to postOne, postTwo, postThree

        $repliesMap[$postReplyThreeFour->getId()] = $postReplyThreeFour;

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/post/show/' . $postFour->getId()
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $results = $response->decodeResponseJson();

        // assert reponse has postReplies data
        $this->assertArrayHasKey('postReplies', $results);
        $this->assertEquals(count($results['postReplies']), count($repliesMap));

        foreach ($results['postReplies'] as $postReply) {

            // assert postReply id is set in repliesMap array
            $postReplyId = $postReply['id'];

            $this->assertArrayHasKey($postReplyId, $repliesMap);

            // assert parent post id
            $parentPostId = $postReply['parentPostId'];

            $this->assertEquals($parentPostId, $repliesMap[$postReplyId]->getParentPostId());

            // assert parent post content
            $this->assertEquals($postReply['parent']['content'], $postsMap[$parentPostId]->getContent());
        }
    }

    public function test_post_show_recent_likes()
    {
        $user = $this->fakeCurrentUserCloak();

        $thread = $this->fakeThread(null, $user->getId());

        $post = $this->fakePost($thread->getId(), $user->getId());

        $postLike = new PostLike();
        $postLike->setPostId($post->getId());
        $postLike->setLikerId($user->getId());
        $postLike->setLikedOn(Carbon::now()->toDateTimeString());
        $postLike->persist();

        $otherUserOne = $this->fakeUserCloak();

        $postLike = new PostLike();
        $postLike->setPostId($post->getId());
        $postLike->setLikerId($otherUserOne->getId());
        $postLike->setLikedOn(Carbon::parse("-5 minutes")->toDateTimeString()); // 2nd most recent liker
        $postLike->persist();

        $otherUserTwo = $this->fakeUserCloak();

        $postLike = new PostLike();
        $postLike->setPostId($post->getId());
        $postLike->setLikerId($otherUserTwo->getId());
        $postLike->setLikedOn(Carbon::parse("-1 minutes")->toDateTimeString()); // 1st most recent liker
        $postLike->persist();

        $otherUserThree = $this->fakeUserCloak();

        $postLike = new PostLike();
        $postLike->setPostId($post->getId());
        $postLike->setLikerId($otherUserThree->getId());
        $postLike->setLikedOn(Carbon::parse("-10 minutes")->toDateTimeString());  // 3rd most recent liker
        $postLike->persist();

        // cache and identity map flush
        $im = $this->app->make(IdentityMap::class);
        $im->empty();
        $postDataMapper = $this->app->make(PostDataMapper::class);
        $postDataMapper->flushCache();

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/post/show/' . $post->getId()
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $postResponse = $response->decodeResponseJson();

        // assert reponse has recentLikes
        $this->assertArrayHasKey('recentLikes', $postResponse);

        // assert 1st recent liker id
        $this->assertEquals($postResponse['recentLikes'][0]['likerId'], $otherUserTwo->getId());

        // assert 2nd recent liker id
        $this->assertEquals($postResponse['recentLikes'][1]['likerId'], $otherUserOne->getId());
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

        // assert the post data was saved in the db
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

    public function test_post_store_validation_fail()
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

        // assert the post data was saved in the db
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

        // assert the post data was marked as soft deleted
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
