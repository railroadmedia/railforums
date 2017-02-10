<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\PostLike;
use Railroad\Railforums\Entities\Thread;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railforums\Services\Posts\ModeratorForumPostService;
use Railroad\Railmap\Helpers\RailmapHelpers;
use Railroad\Railmap\IdentityMap\IdentityMap;

class ModeratorForumPostServiceTest extends TestCase
{
    /**
     * @var ModeratorForumPostService
     */
    private $classBeingTested;

    /**
     * @var UserCloak
     */
    private $currentUserCloak;

    public function setUp()
    {
        parent::setUp();

        $this->classBeingTested = app(ModeratorForumPostService::class);
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

    public function test_get_posts_can_see_hidden()
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
            15
        );

        $responseEntities = $this->classBeingTested->getPosts(
            15,
            1,
            $thread->getId()
        );

        $this->assertEquals($expectedEntities, $responseEntities);
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

        $this->assertEquals(15, $responseCount);
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

        $this->assertTrue($response);

        $this->assertDatabaseHas(
            'forum_posts',
            [
                'id' => $post->getId(),
                'content' => $post->getContent(),
            ]
        );
    }

    public function test_set_post_state_published()
    {
        $userCloak = $this->fakeUserCloak();

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->persist();

        $post = new Post();
        $post->randomize();
        $post->setState(Post::STATE_HIDDEN);
        $post->setThreadId($thread->getId());
        $post->setAuthorId($userCloak->getId());
        $post->persist();

        $this->classBeingTested->setPostAsPublished($thread->getId());

        $this->assertDatabaseHas(
            'forum_posts',
            ['id' => $post->getId(), 'state' => Post::STATE_PUBLISHED]
        );
    }

    public function test_set_post_state_hidden()
    {
        $userCloak = $this->fakeUserCloak();

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($thread->getId());
        $post->setAuthorId($userCloak->getId());
        $post->persist();

        $thread->persist();

        $this->classBeingTested->setPostAsHidden($thread->getId());

        $this->assertDatabaseHas(
            'forum_posts',
            ['id' => $thread->getId(), 'state' => Thread::STATE_HIDDEN]
        );
    }

    public function test_update_post_content_any_author()
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

    public function test_destroy_any_post()
    {
        Carbon::setTestNow(Carbon::now());

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

        $response = $this->classBeingTested->destroyPost($post->getId());

        $this->assertTrue($response);

        $this->assertDatabaseHas(
            'forum_posts',
            [
                'id' => $post->getId(),
                'deleted_at' => Carbon::now()->toDateTimeString(),
            ]
        );
    }

    public function test_post_likes_link()
    {
        Carbon::setTestNow(Carbon::now());

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

        $postLikes = [];

        for ($i = 0; $i < 10; $i++) {
            $user = $this->fakeUserCloak();

            $postLike = new PostLike();
            $postLike->setPostId($post->getId());
            $postLike->setLikerId($user->getId());
            $postLike->setLikedOn(
                \Carbon\Carbon::instance($this->faker->dateTime)->toDateTimeString()
            );
            $postLike->persist();

            $postLikes[] = $postLike;
        }

        IdentityMap::empty();

        $responseEntities = $this->classBeingTested->getPosts(1, 1, $thread->getId());

        $this->assertEquals(10, $responseEntities[0]->getLikeCount());
        $this->assertEquals(
            array_slice(
                RailmapHelpers::sortEntitiesByDateAttribute(
                    $postLikes,
                    'likedOn',
                    'desc'
                ),
                0,
                3
            ),
            $responseEntities[0]->getRecentLikes()
        );
    }
}