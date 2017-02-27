<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\PostLike;
use Railroad\Railforums\Entities\Thread;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railforums\Services\Posts\UserForumPostService;
use Railroad\Railmap\Helpers\RailmapHelpers;

class UserForumPostServiceTest extends TestCase
{
    /**
     * @var UserForumPostService
     */
    private $classBeingTested;

    /**
     * @var UserCloak
     */
    private $currentUserCloak;

    public function setUp()
    {
        parent::setUp();

        $this->classBeingTested = app(UserForumPostService::class);
        $this->currentUserCloak = $this->fakeCurrentUserCloak(UserCloak::PERMISSION_LEVEL_USER);
    }

    public function test_get_posts_1_page()
    {
        $entities = [];

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
                'asc'
            ),
            0,
            5
        );

        $responseEntities = $this->classBeingTested->getPosts(
            5,
            1,
            $thread->getId()
        );

        $this->assertEquals($expectedEntities, $responseEntities);
    }

    public function test_get_posts_2_pages()
    {
        $this->databaseManager->connection()->enableQueryLog();

        $entities = [];

        $user = $this->fakeUserCloak();

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->setAuthorId($user->getId());
        $thread->persist();

        for ($x = 0; $x < 7; $x++) {
            $user = $this->fakeUserCloak();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($thread->getId());
            $post->setAuthorId($user->getId());
            $post->setState(Post::STATE_PUBLISHED);
            $post->persist();

            $entities[] = $post;

            for ($i = 0; $i < 3; $i++) {
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
                'asc'
            ),
            0,
            5
        );

        $responseEntities = $this->classBeingTested->getPosts(
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
                'asc'
            ),
            5,
            5
        );

        $responseEntities = $this->classBeingTested->getPosts(
            5,
            2,
            $thread->getId()
        );

        $this->assertEquals($expectedEntities, $responseEntities);
    }

    public function test_get_posts_cant_see_hidden()
    {
        $entities = [];

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

        for ($x = 0; $x < 12; $x++) {
            $user = $this->fakeUserCloak();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($thread->getId());
            $post->setAuthorId($user->getId());
            $post->setState(Post::STATE_HIDDEN);
            $post->persist();
        }

        // Page 1
        $expectedEntities = array_slice(
            RailmapHelpers::sortEntitiesByDateAttribute(
                $entities,
                'publishedOn',
                'asc'
            ),
            0,
            5
        );

        $responseEntities = $this->classBeingTested->getPosts(
            5,
            1,
            $thread->getId()
        );

        $this->assertEquals($expectedEntities, $responseEntities);
    }

    public function test_get_posts_none_exist()
    {
        $responseEntities = $this->classBeingTested->getPosts(
            5,
            1,
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

        $responseCount = $this->classBeingTested->getThreadPostCount($thread->getId());

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

        $responseCount = $this->classBeingTested->getThreadPostCount($thread->getId());

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

        $responseCount = $this->classBeingTested->getThreadPostCount($thread->getId());

        $this->assertEquals(8, $responseCount);
    }

    public function test_update_post_content()
    {
        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($thread->getId());
        $post->setAuthorId($this->currentUserCloak->getId());
        $post->setState(Post::STATE_PUBLISHED);
        $post->persist();

        $newContent = $this->faker->sentence();

        $response = $this->classBeingTested->updatePostContent($post->getId(), $newContent);

        $this->assertDatabaseHas(
            'forum_posts',
            [
                'id' => $post->getId(),
                'content' => $post->getContent(),
            ]
        );
    }

    public function test_update_post_content_author_only()
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

        $this->assertDatabaseMissing(
            'forum_posts',
            [
                'id' => $post->getId(),
                'content' => $newContent,
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

        $post = $this->classBeingTested->createPost(
            $content,
            $promptingPostId,
            $threadId
        );

        $this->assertDatabaseHas(
            'forum_posts',
            [
                'content' => $post->getContent(),
                'prompting_post_id' => $promptingPostId,
                'thread_id' => $threadId,
                'author_id' => $this->currentUserCloak->getId(),
                'state' => Thread::STATE_PUBLISHED,
                'published_on' => Carbon::now(),
            ]
        );
    }
}