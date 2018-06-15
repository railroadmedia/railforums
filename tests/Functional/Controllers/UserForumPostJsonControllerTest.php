<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Entities\PostLike;
use Railroad\Railforums\Entities\PostReply;
use Railroad\Railforums\DataMappers\PostDataMapper;
use Railroad\Railmap\IdentityMap\IdentityMap;
use Railroad\Railforums\Services\ConfigService;

class UserForumPostJsonControllerTest extends TestCase
{
    const API_PREFIX = '/forums';

    protected function setUp()
    {
        $this->setDefaultConnection('mysql');

        parent::setUp();
    }

    public function test_post_like_with_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        $this->permissionServiceMock->method('can')->willReturn(true);

        /** @var array $post */
        $post = $this->fakePost($thread['id'], $user->getId());

        $response = $this->call(
            'PUT',
            self::API_PREFIX . '/post/like/' . $post['id']
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $this->assertArraySubset(
            [
                'liker_id' => $user->getId(),
                'post_id' => $post['id']
            ],
            $response->decodeResponseJson()
        );

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
            self::API_PREFIX . '/post/like/' . $post['id']
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());

        // assert the post like data was not saved in the db
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
            self::API_PREFIX . '/post/like/' . $postId
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
            self::API_PREFIX . '/post/unlike/' . $post['id']
        );

        // assert response status code
        $this->assertEquals(204, $response->getStatusCode());

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
            self::API_PREFIX . '/post/unlike/' . $post['id']
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

    public function test_post_unlike_not_exists()
    {
        $user = $this->fakeCurrentUserCloak();
        $postId = rand(0, 32767);

        $this->permissionServiceMock->method('can')->willReturn(true);

        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/post/unlike/' . $postId
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_post_index_with_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $threadOne */
        $threadOne = $this->fakeThread($category['id'], $user->getId());

        $posts = [];

        for ($i=0; $i < 20; $i++) {
            /** @var array $post */
            $post = $this->fakePost($threadOne['id'], $user->getId());

            $posts[$post['id']] = $post;
        }

        /** @var array $threadTwo */
        $threadTwo = $this->fakeThread($category['id'], $user->getId());

        for ($i=0; $i < 10; $i++) {
            /** @var array $post */
            $post = $this->fakePost($threadTwo['id'], $user->getId());

            $posts[$post['id']] = $post;
        }

        $payload = [
            'amount' => 10,
            'page' => 1,
            'thread_id' => $threadOne['id']
        ];

        $this->permissionServiceMock->method('can')->willReturn(true);

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/post/index',
            $payload
        );

        $this->assertTrue(true);

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $results = $response->decodeResponseJson();

        // assert reponse posts count is the requested amount
        $this->assertEquals(count($results['posts']), $payload['amount']);

        // assert reponse posts count
        $this->assertEquals($results['count'], 20);

        // assert reponse posts have the requested category
        foreach ($results['posts'] as $post) {
            $this->assertEquals($post['thread_id'], $payload['thread_id']);
        }
    }

    public function test_post_show_with_permission()
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
            self::API_PREFIX . '/post/show/' . $post['id']
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $this->assertArraySubset(
            ['content' => $post['content']],
            $response->decodeResponseJson()
        );
    }

    public function test_post_show_without_permission()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        /** @var array $post */
        $post = $this->fakePost($thread['id'], $user->getId());

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/post/show/' . $post['id']
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());

    }

    public function test_post_show_with_replies()
    {
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        $postsMap = []; // holds the posts, keys are posts id's

        /** @var array $postOne */
        $postOne = $this->fakePost($thread['id'], $user->getId());
        $postsMap[$postOne['id']] = $postOne;

        /** @var array $postTwo */
        $postTwo = $this->fakePost($thread['id'], $user->getId());
        $postsMap[$postTwo['id']] = $postTwo;

        /** @var array $postThree */
        $postThree = $this->fakePost($thread['id'], $user->getId());
        $postsMap[$postThree['id']] = $postThree;

        /** @var array $postFour */
        $postFour = $this->fakePost($thread['id'], $user->getId());

        $repliesMap = []; // holds the replies, keys are parent_post_id's

        $postReplyOneFour = [
            'child_post_id' => $postFour['id'],
            'parent_post_id' => $postOne['id']
        ];

        $this->databaseManager
            ->table(ConfigService::$tablePostReplies)
            ->insertGetId($postReplyOneFour);

        $repliesMap[$postReplyOneFour['parent_post_id']] = $postReplyOneFour;

        $postReplyTwoFour = [
            'child_post_id' => $postFour['id'],
            'parent_post_id' => $postTwo['id']
        ];

        $this->databaseManager
            ->table(ConfigService::$tablePostReplies)
            ->insertGetId($postReplyTwoFour);

        $repliesMap[$postReplyTwoFour['parent_post_id']] = $postReplyTwoFour;

        $postReplyThreeFour = [
            'child_post_id' => $postFour['id'],
            'parent_post_id' => $postThree['id']
        ];

        $this->databaseManager
            ->table(ConfigService::$tablePostReplies)
            ->insertGetId($postReplyThreeFour);

        $repliesMap[$postReplyThreeFour['parent_post_id']] = $postReplyThreeFour;

        // ^^ postFour is set to quote/reply to postOne, postTwo, postThree

        $this->permissionServiceMock->method('can')->willReturn(true);

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/post/show/' . $postFour['id']
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $results = $response->decodeResponseJson();

        /*
        // response post format:
        $post = [
            // current post fields

            'reply_parents' => [
                [
                    // parent post fields
                ],
                [
                    // parent post fields
                ],
                //...
            ]
        ]
        */

        // assert reponse has postReplies data
        $this->assertArrayHasKey('reply_parents', $results);
        $this->assertEquals(
            count($results['reply_parents']),
            count($repliesMap)
        );

        foreach ($results['reply_parents'] as $postReply) {

            // assert postReply parent id is set in repliesMap array
            $postReplyParentId = $postReply['id'];

            $this->assertArrayHasKey($postReplyParentId, $repliesMap);

            // assert parent post content
            $this->assertEquals(
                $postReply['content'],
                $postsMap[$postReplyParentId]['content']
            );
        }
    }

    public function test_post_show_recent_likes()
    {
        // 1st most recent liker
        $user = $this->fakeCurrentUserCloak();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user->getId());

        /** @var array $post */
        $post = $this->fakePost($thread['id'], $user->getId());

        $now = Carbon::now()->toDateTimeString();

        $postLike = [
            'post_id' => $post['id'],
            'liker_id' => $user->getId(),
            'liked_on' => $now,
            'created_at' => $now,
            'updated_at' => $now
        ];

        $this->databaseManager
            ->table(ConfigService::$tablePostLikes)
            ->insertGetId($postLike);

        // 3rd most recent liker
        $otherUserOne = $this->fakeUserCloak();

        $fiveMinutesAgo = Carbon::parse("-5 minutes")->toDateTimeString();

        $postLike = [
            'post_id' => $post['id'],
            'liker_id' => $otherUserOne->getId(),
            'liked_on' => $fiveMinutesAgo,
            'created_at' => $fiveMinutesAgo,
            'updated_at' => $fiveMinutesAgo
        ];

        $this->databaseManager
            ->table(ConfigService::$tablePostLikes)
            ->insertGetId($postLike);

        // 2nd most recent liker
        $otherUserTwo = $this->fakeUserCloak();

        $oneMinuteAgo = Carbon::parse("-1 minutes")->toDateTimeString();

        $postLike = [
            'post_id' => $post['id'],
            'liker_id' => $otherUserTwo->getId(),
            'liked_on' => $oneMinuteAgo,
            'created_at' => $oneMinuteAgo,
            'updated_at' => $oneMinuteAgo
        ];

        $this->databaseManager
            ->table(ConfigService::$tablePostLikes)
            ->insertGetId($postLike);

        // 4th most recent liker
        $otherUserThree = $this->fakeUserCloak();

        $tenMinutesAgo = Carbon::parse("-10 minutes")->toDateTimeString();

        $postLike = [
            'post_id' => $post['id'],
            'liker_id' => $otherUserThree->getId(),
            'liked_on' => $tenMinutesAgo,
            'created_at' => $tenMinutesAgo,
            'updated_at' => $tenMinutesAgo
        ];

        $this->databaseManager
            ->table(ConfigService::$tablePostLikes)
            ->insertGetId($postLike);

        // 5th most recent liker
        $otherUserFour = $this->fakeUserCloak();

        $twentyMinutesAgo = Carbon::parse("-20 minutes")->toDateTimeString();

        $postLike = [
            'post_id' => $post['id'],
            'liker_id' => $otherUserFour->getId(),
            'liked_on' => $twentyMinutesAgo,
            'created_at' => $twentyMinutesAgo,
            'updated_at' => $twentyMinutesAgo
        ];

        $this->databaseManager
            ->table(ConfigService::$tablePostLikes)
            ->insertGetId($postLike);

        $this->permissionServiceMock->method('can')->willReturn(true);

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/post/show/' . $post['id']
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $postResponse = $response->decodeResponseJson();

        // assert reponse like count
        $this->assertEquals($postResponse['like_count'], 5);

        // assert 1st recent liker id
        $this->assertEquals($postResponse['liker_1_id'], $user->getId());

        // assert 1st recent liker display name
        $this->assertEquals($postResponse['liker_1_display_name'], $user->getDisplayName());

        // assert 2nd recent liker id
        $this->assertEquals($postResponse['liker_2_id'], $otherUserTwo->getId());

        // assert 2nd recent liker display name
        $this->assertEquals($postResponse['liker_2_display_name'], $otherUserTwo->getDisplayName());

        // assert 3rd recent liker id
        $this->assertEquals($postResponse['liker_3_id'], $otherUserOne->getId());

        // assert 3rd recent liker display name
        $this->assertEquals($postResponse['liker_3_display_name'], $otherUserOne->getDisplayName());
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

    // public function test_post_store()
    // {
    //     $user = $this->fakeCurrentUserCloak();

    //     $category = $this->fakeCategory();

    //     $thread = $this->fakeThread($category->getId(), $user->getId());

    //     $postData = [
    //         'content' => $this->faker->sentence(),
    //         'thread_id' => $thread->getId()
    //     ];

    //     $response = $this->call(
    //         'PUT',
    //         self::API_PREFIX . '/post/store',
    //         $postData
    //     );

    //     // assert response status code
    //     $this->assertEquals(200, $response->getStatusCode());

    //     // assert the post data was saved in the db
    //     $this->assertDatabaseHas(
    //         'forum_posts',
    //         [
    //             'content' => $postData['content'],
    //             'thread_id' => $postData['thread_id']
    //         ]
    //     );

    //     // assert response data
    //     $response->assertJsonFragment([
    //         'content' => $postData['content'],
    //         'threadId' => $postData['thread_id']
    //     ]);
    // }

    // public function test_post_store_with_replies()
    // {
    //     $user = $this->fakeCurrentUserCloak();

    //     $category = $this->fakeCategory();

    //     $thread = $this->fakeThread($category->getId(), $user->getId());

    //     $postOne = $this->fakePost($thread->getId(), $user->getId());
    //     $postTwo = $this->fakePost($thread->getId(), $user->getId());
    //     $postThree = $this->fakePost($thread->getId(), $user->getId());

    //     $postData = [
    //         'content' => $this->faker->sentence(),
    //         'thread_id' => $thread->getId(),
    //         'parent_ids' => [
    //             $postOne->getId(),
    //             $postTwo->getId(),
    //             $postThree->getId()
    //         ]
    //     ];

    //     $response = $this->call(
    //         'PUT',
    //         self::API_PREFIX . '/post/store',
    //         $postData
    //     );

    //     // assert response status code
    //     $this->assertEquals(200, $response->getStatusCode());

    //     // assert the post data was saved in the db
    //     $this->assertDatabaseHas(
    //         'forum_posts',
    //         [
    //             'content' => $postData['content'],
    //             'thread_id' => $postData['thread_id']
    //         ]
    //     );

    //     // assert response data
    //     $response->assertJsonFragment([
    //         'content' => $postData['content'],
    //         'threadId' => $postData['thread_id']
    //     ]);

    //     $postResponse = $response->decodeResponseJson();

    //     // assert postOne is marked as parent in db
    //     $this->assertDatabaseHas(
    //         'forum_post_replies',
    //         [
    //             'parent_post_id' => $postOne->getId(),
    //             'child_post_id' => $postResponse['id']
    //         ]
    //     );

    //     // assert postTwo is marked as parent in db
    //     $this->assertDatabaseHas(
    //         'forum_post_replies',
    //         [
    //             'parent_post_id' => $postTwo->getId(),
    //             'child_post_id' => $postResponse['id']
    //         ]
    //     );

    //     // assert postThree is marked as parent in db
    //     $this->assertDatabaseHas(
    //         'forum_post_replies',
    //         [
    //             'parent_post_id' => $postThree->getId(),
    //             'child_post_id' => $postResponse['id']
    //         ]
    //     );
    // }

    // public function test_post_store_validation_fail()
    // {
    //     $response = $this->call(
    //         'PUT',
    //         self::API_PREFIX . '/post/store',
    //         []
    //     );

    //     // assert response status code
    //     $this->assertEquals(422, $response->getStatusCode());

    //     // assert response validation error messages
    //     $this->assertEquals([
    //         [
    //             "source" => "content",
    //             "detail" => "The content field is required.",
    //         ],
    //         [
    //             "source" => "thread_id",
    //             "detail" => "The thread id field is required.",
    //         ]
    //     ], $response->decodeResponseJson()['errors']);
    // }

    // public function test_post_update()
    // {
    //     $user = $this->fakeCurrentUserCloak();

    //     $thread = $this->fakeThread(null, $user->getId());

    //     $post = $this->fakePost($thread->getId(), $user->getId());

    //     $newContent = $this->faker->sentence();

    //     $response = $this->call(
    //         'PATCH',
    //         self::API_PREFIX . '/post/update/' . $post->getId(),
    //         ['content' => $newContent]
    //     );

    //     // assert response status code
    //     $this->assertEquals(200, $response->getStatusCode());

    //     // assert the post data was saved in the db
    //     $this->assertDatabaseHas(
    //         'forum_posts',
    //         [
    //             'id' => $post->getId(),
    //             'content' => $newContent
    //         ]
    //     );

    //     // assert response data
    //     $response->assertJsonFragment([
    //         'content' => $newContent,
    //         'id' => $post->getId()
    //     ]);
    // }

    // public function test_post_update_validation_fail()
    // {
    //     $user = $this->fakeCurrentUserCloak();
    //     $thread = $this->fakeThread(null, $user->getId());
    //     $post = $this->fakePost($thread->getId(), $user->getId());
    //     $newContent = $this->faker->sentence();

    //     $response = $this->call(
    //         'PATCH',
    //         self::API_PREFIX . '/post/update/' . $post->getId(),
    //         []
    //     );

    //     // assert response status code
    //     $this->assertEquals(422, $response->getStatusCode());

    //     // assert response validation error messages
    //     $this->assertEquals([
    //         [
    //             "source" => "content",
    //             "detail" => "The content field is required.",
    //         ]
    //     ], $response->decodeResponseJson()['errors']);

    //     // assert new content was not saved in db
    //     $this->assertDatabaseMissing(
    //         'forum_posts',
    //         [
    //             'id' => $post->getId(),
    //             'content' => $newContent
    //         ]
    //     );
    // }

    // public function test_post_update_not_found()
    // {
    //     $response = $this->call(
    //         'PATCH',
    //         self::API_PREFIX . '/post/update/' . rand(0, 32767),
    //         ['content' => $this->faker->sentence()]
    //     );

    //     // assert response status code
    //     $this->assertEquals(404, $response->getStatusCode());
    // }

    // public function test_post_delete()
    // {
    //     $user = $this->fakeCurrentUserCloak();
    //     $category = $this->fakeCategory();
    //     $thread = $this->fakeThread($category->getId(), $user->getId());
    //     $post = $this->fakePost($thread->getId(), $user->getId());

    //     $response = $this->call(
    //         'DELETE',
    //         self::API_PREFIX . '/post/delete/' . $post->getId()
    //     );

    //     // assert response status code
    //     $this->assertEquals(204, $response->getStatusCode());

    //     // assert the post data was marked as soft deleted
    //     $this->assertSoftDeleted(
    //         'forum_posts',
    //         [
    //             'id' => $post->getId()
    //         ]
    //     );
    // }

    // public function test_post_delete_not_found()
    // {
    //     $response = $this->call(
    //         'DELETE',
    //         self::API_PREFIX . '/post/delete/' . rand(0, 32767)
    //     );

    //     // assert response status code
    //     $this->assertEquals(404, $response->getStatusCode());
    // }
}
