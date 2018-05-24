<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\PostLike;
use Railroad\Railforums\Entities\Thread;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railforums\Services\PostLikes\ForumPostLikeService;

class ForumPostLikeServiceTest extends TestCase
{
    /**
     * @var ForumPostLikeService
     */
    private $classBeingTested;

    /**
     * @var UserCloak
     */
    private $currentUserCloak;

    public function setUp()
    {
        parent::setUp();

        $this->classBeingTested = app(ForumPostLikeService::class);
        $this->currentUserCloak = $this->fakeCurrentUserCloak();
    }

    public function test_like_post_new()
    {
        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($thread->getId());
        $post->setState(Post::STATE_PUBLISHED);
        $post->persist();

        $postLike = $this->classBeingTested->likePost($post->getId());

        $this->assertDatabaseHas(
            'forum_post_likes',
            ['post_id' => $post->getId(), 'liker_id' => $this->currentUserCloak->getId()]
        );

        // $this->assertEquals([$postLike], $post->getRecentLikes());
        $this->assertEquals(1, $post->getLikeCount());
    }

    public function test_like_post_exists()
    {
        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($thread->getId());
        $post->setState(Post::STATE_PUBLISHED);
        $post->persist();

        $postLike = new PostLike();
        $postLike->setPostId($post->getId());
        $postLike->setLikerId($this->currentUserCloak->getId());
        $postLike->setLikedOn(Carbon::now()->toDateTimeString());
        $postLike->persist();

        $postLike = $this->classBeingTested->likePost($post->getId());

        $this->assertDatabaseHas(
            'forum_post_likes',
            ['post_id' => $post->getId(), 'liker_id' => $this->currentUserCloak->getId()]
        );

        // $this->assertEquals([$postLike], $post->getRecentLikes());
        $this->assertEquals(1, $post->getLikeCount());
    }

    public function test_un_like_post_exists()
    {
        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($thread->getId());
        $post->setState(Post::STATE_PUBLISHED);
        $post->persist();

        $postLike = new PostLike();
        $postLike->setPostId($post->getId());
        $postLike->setLikerId($this->currentUserCloak->getId());
        $postLike->setLikedOn(Carbon::now()->toDateTimeString());
        $postLike->persist();

        $this->assertDatabaseHas(
            'forum_post_likes',
            ['post_id' => $post->getId(), 'liker_id' => $this->currentUserCloak->getId()]
        );

        $this->classBeingTested->unLikePost($post->getId());

        $this->assertDatabaseMissing(
            'forum_post_likes',
            ['post_id' => $post->getId(), 'liker_id' => $this->currentUserCloak->getId()]
        );

        $this->assertEquals(0, $post->getLikeCount());
    }

    public function test_un_like_post_does_not_exists()
    {
        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($thread->getId());
        $post->setState(Post::STATE_PUBLISHED);
        $post->persist();

        $this->classBeingTested->unLikePost($post->getId());

        $this->assertDatabaseMissing(
            'forum_post_likes',
            ['post_id' => $post->getId(), 'liker_id' => $this->currentUserCloak->getId()]
        );

        $this->assertEquals(0, $post->getLikeCount());
    }
}