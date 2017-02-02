<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\PostLike;
use Railroad\Railforums\Entities\Thread;
use Railroad\Railforums\Services\ForumPostService;
use Railroad\Railmap\Helpers\RailmapHelpers;

class ForumPostServiceTest extends TestCase
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
        $entities = [];

        $currentUserData = $this->fakeUser();

        $userData = $this->fakeUser();

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->setAuthorId($userData['id']);
        $thread->persist();

        for ($x = 0; $x < 13; $x++) {
            $userData = $this->fakeUser();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($thread->getId());
            $post->setAuthorId($userData['id']);
            $post->setState(Post::STATE_PUBLISHED);
            $post->persist();

            $entities[] = $post;

            $postLike = new PostLike();
            $postLike->setPostId($post->getId());
            $postLike->setLikerId($userData['id']);
            $postLike->setLikedOn(Carbon::now()->toDateTimeString());
            $postLike->persist();
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
            $currentUserData['id'],
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
            $currentUserData['id'],
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
            $currentUserData['id'],
            $thread->getId()
        );

        $this->assertEquals($expectedEntities, $responseEntities);
    }

    public function test_get_posts_sorted_paginated_single_page()
    {
        $entities = [];

        $currentUserData = $this->fakeUser();

        $userData = $this->fakeUser();

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->setAuthorId($userData['id']);
        $thread->persist();

        for ($x = 0; $x < 3; $x++) {
            $userData = $this->fakeUser();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($thread->getId());
            $post->setAuthorId($userData['id']);
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
            $currentUserData['id'],
            $thread->getId()
        );

        $this->assertEquals($expectedEntities, $responseEntities);
    }

    public function test_get_posts_sorted_paginated_none_exist()
    {
        $currentUserData = $this->fakeUser();

        $responseEntities = $this->classBeingTested->getPostsSortedPaginated(
            5,
            1,
            $currentUserData['id'],
            rand()
        );

        $this->assertEmpty($responseEntities);
    }

    public function test_set_post_state_published_shows_in_list()
    {
        $currentUserData = $this->fakeUser();

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_DRAFT);
        $thread->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($thread->getId());
        $post->setAuthorId($currentUserData['id']);
        $post->persist();

        $thread->persist();

        $this->classBeingTested->setPostAsPublished($thread->getId());

        $this->assertDatabaseHas(
            'forum_posts',
            ['id' => $thread->getId(), 'state' => Thread::STATE_PUBLISHED]
        );

        $responseEntities = $this->classBeingTested->getPostsSortedPaginated(
            1,
            1,
            $currentUserData['id'],
            $thread->getId()
        );

        $this->assertEquals([$post], $responseEntities);
    }

    public function test_set_post_state_hidden_hide_from_list()
    {
        $currentUserData = $this->fakeUser();

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($thread->getId());
        $post->setAuthorId($currentUserData['id']);
        $post->persist();

        $thread->persist();

        $this->classBeingTested->setPostAsHidden($thread->getId());

        $this->assertDatabaseHas(
            'forum_posts',
            ['id' => $thread->getId(), 'state' => Thread::STATE_HIDDEN]
        );

        $responseEntities = $this->classBeingTested->getPostsSortedPaginated(
            1,
            1,
            $currentUserData['id'],
            $thread->getId()
        );

        $this->assertEquals([], $responseEntities);
    }

    public function test_get_post_count()
    {
        $entities = [];

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->persist();

        $userData = $this->fakeUser();

        for ($i = 0; $i < 13; $i++) {
            $post = new Post();
            $post->randomize();
            $post->setThreadId($thread->getId());
            $post->setAuthorId($userData['id']);
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
            $userData = $this->fakeUser();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($thread->getId());
            $post->setAuthorId($userData['id']);
            $post->setState(Post::STATE_PUBLISHED);
            $post->persist();

            $entities[] = $post;
        }

        for ($i = 0; $i < 6; $i++) {
            $userData = $this->fakeUser();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($thread->getId());
            $post->setAuthorId($userData['id']);
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
            $userData = $this->fakeUser();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($thread->getId());
            $post->setAuthorId($userData['id']);
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

        $userData = $this->fakeUser();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($thread->getId());
        $post->setAuthorId($userData['id']);
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