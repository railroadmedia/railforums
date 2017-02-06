<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\PostLike;
use Railroad\Railforums\Entities\Thread;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railforums\Services\ForumPostService;
use Railroad\Railmap\Helpers\RailmapHelpers;
use Railroad\Railmap\IdentityMap\IdentityMap;

class ForumPostServiceAdminTest extends TestCase
{
    /**
     * @var ForumPostService
     */
    private $classBeingTested;

    public function setUp()
    {
        parent::setUp();

        $this->classBeingTested = app(ForumPostService::class);
    }

    public function test_get_posts_sorted_paginated()
    {
        $this->databaseManager->connection()->enableQueryLog();

        $entities = [];

        $user = $this->fakeUserCloak();

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->setAuthorId($user->getId());
        $thread->persist();

        for ($x = 0; $x < 13; $x++) {
            $user = $this->fakeUserCloak();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($thread->getId());
            $post->setAuthorId($user->getId());
            $post->setState(Post::STATE_PUBLISHED);
            $post->persist();

            $entities[] = $post;

            for ($i = 0; $i < 12; $i++) {
                $user = $this->fakeUserCloak();

                $postLike = new PostLike();
                $postLike->setPostId($post->getId());
                $postLike->setLikerId($user->getId());
                $postLike->setLikedOn(Carbon::now()->toDateTimeString());
                $postLike->persist();
            }
        }

        // Page 1
        $expectedEntities = array_slice(
            RailmapHelpers::sortEntitiesByDateAttribute(
                $entities,
                'publishedOn',
                'desc'
            ),
            0,
            5
        );

        $responseEntities = $this->classBeingTested->getPostsSortedPaginated(
            5,
            1,
            $thread->getId()
        );

        $this->assertEquals($expectedEntities, $responseEntities);

        // Page 2
        $expectedEntities = array_slice(
            RailmapHelpers::sortEntitiesByDateAttribute(
                $entities,
                'publishedOn',
                'desc'
            ),
            5,
            5
        );

        $responseEntities = $this->classBeingTested->getPostsSortedPaginated(
            5,
            2,
            $thread->getId()
        );

        $this->assertEquals($expectedEntities, $responseEntities);

        // Page 3
        $expectedEntities = array_slice(
            RailmapHelpers::sortEntitiesByDateAttribute(
                $entities,
                'publishedOn',
                'desc'
            ),
            10,
            5
        );

        $responseEntities = $this->classBeingTested->getPostsSortedPaginated(
            5,
            3,
            $thread->getId()
        );

        $this->assertEquals($expectedEntities, $responseEntities);
    }

    public function test_get_posts_sorted_paginated_single_page()
    {
        $entities = [];

        $currentUser = $this->fakeCurrentUserCloak();

        $user = $this->fakeUserCloak();

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->setAuthorId($user->getId());
        $thread->persist();

        for ($x = 0; $x < 3; $x++) {
            $user = $this->fakeUserCloak();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($thread->getId());
            $post->setAuthorId($user->getId());
            $post->setState(Post::STATE_PUBLISHED);
            $post->persist();

            $entities[] = $post;
        }

        // Page 1
        $expectedEntities = array_slice(
            RailmapHelpers::sortEntitiesByDateAttribute(
                $entities,
                'publishedOn',
                'desc'
            ),
            0,
            5
        );

        $responseEntities = $this->classBeingTested->getPostsSortedPaginated(
            5,
            1,
            $currentUser->getId(),
            $thread->getId()
        );

        $this->assertEquals($expectedEntities, $responseEntities);
    }

    public function test_get_posts_sorted_paginated_none_exist()
    {
        $currentUser = $this->fakeCurrentUserCloak();

        $responseEntities = $this->classBeingTested->getPostsSortedPaginated(
            5,
            1,
            $currentUser->getId(),
            rand()
        );

        $this->assertEmpty($responseEntities);
    }

    public function test_get_post_count()
    {
        $entities = [];

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->persist();

        $user = $this->fakeUserCloak();

        for ($i = 0; $i < 13; $i++) {
            $post = new Post();
            $post->randomize();
            $post->setThreadId($thread->getId());
            $post->setAuthorId($user->getId());
            $post->setState(Post::STATE_PUBLISHED);
            $post->persist();

            $entities[] = $post;
        }

        $responseCount = $this->classBeingTested->getPostCount($thread->getId());

        $this->assertEquals(13, $responseCount);
    }

    public function test_get_post_count_some_hidden()
    {
        $entities = [];

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->persist();

        for ($i = 0; $i < 9; $i++) {
            $user = $this->fakeUserCloak();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($thread->getId());
            $post->setAuthorId($user->getId());
            $post->setState(Post::STATE_PUBLISHED);
            $post->persist();

            $entities[] = $post;
        }

        for ($i = 0; $i < 6; $i++) {
            $user = $this->fakeUserCloak();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($thread->getId());
            $post->setAuthorId($user->getId());
            $post->setState(Post::STATE_HIDDEN);
            $post->persist();

            $entities[] = $post;
        }

        $responseCount = $this->classBeingTested->getPostCount($thread->getId());

        $this->assertEquals(9, $responseCount);
    }

    public function test_get_post_count_one_deleted()
    {
        $entities = [];

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->persist();

        for ($i = 0; $i < 9; $i++) {
            $user = $this->fakeUserCloak();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($thread->getId());
            $post->setAuthorId($user->getId());
            $post->setState(Post::STATE_PUBLISHED);
            $post->persist();

            $entities[] = $post;
        }

        $entities[0]->destroy();

        $responseCount = $this->classBeingTested->getPostCount($thread->getId());

        $this->assertEquals(8, $responseCount);
    }

    public function test_update_post_content()
    {
        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->persist();

        $user = $this->fakeUserCloak();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($thread->getId());
        $post->setAuthorId($user->getId());
        $post->setState(Post::STATE_PUBLISHED);
        $post->persist();

        $newContent = $this->faker->sentence();

        $response = $this->classBeingTested->updatePostContent($post->getId(), $newContent);

        $this->assertTrue($response);

        $this->assertDatabaseHas(
            'forum_posts',
            [
                'id' => $post->getId(),
                'content' => $post->getContent(),
            ]
        );
    }

    public function test_create_post()
    {
        Carbon::setTestNow(Carbon::now());

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->persist();

        $content = $this->faker->paragraph();
        $promptingPostId = $this->faker->randomNumber();
        $threadId = $thread->getId();
        $authorId = $this->faker->randomNumber();

        $post = $this->classBeingTested->createPost(
            $content,
            $promptingPostId,
            $threadId,
            $authorId
        );

        $this->assertDatabaseHas(
            'forum_posts',
            [
                'content' => $post->getContent(),
                'prompting_post_id' => $promptingPostId,
                'thread_id' => $threadId,
                'author_id' => $authorId,
                'state' => Thread::STATE_PUBLISHED,
                'published_on' => Carbon::now(),
            ]
        );
    }
}