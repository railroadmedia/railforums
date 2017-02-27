<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\Thread;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railforums\Services\ThreadFollows\ThreadFollowService;

class ThreadFollowServiceTest extends TestCase
{
    /**
     * @var ThreadFollowService
     */
    private $classBeingTested;

    /**
     * @var UserCloak
     */
    private $currentUserCloak;

    public function setUp()
    {
        parent::setUp();

        $this->classBeingTested = app(ThreadFollowService::class);
        $this->currentUserCloak = $this->fakeCurrentUserCloak(UserCloak::PERMISSION_LEVEL_MODERATOR);
    }

    public function test_follow_thread()
    {
        $user = $this->fakeUserCloak();

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->setAuthorId($user->getId());
        $thread->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($thread->getId());
        $post->setAuthorId($user->getId());
        $post->persist();

        $response = $this->classBeingTested->follow($thread->getId(), $user->getId());

        $this->assertTrue($response);

        $this->assertDatabaseHas(
            'forum_thread_follows',
            [
                'thread_id' => $thread->getId(),
                'follower_id' => $user->getId(),
                'followed_on' => Carbon::now()->toDateTimeString()
            ]
        );
    }

    public function test_follow_thread_exists()
    {
        $user = $this->fakeUserCloak();

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->setAuthorId($user->getId());
        $thread->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($thread->getId());
        $post->setAuthorId($user->getId());
        $post->persist();

        $this->classBeingTested->follow($thread->getId(), $user->getId());

        $response = $this->classBeingTested->follow($thread->getId(), $user->getId());

        $this->assertTrue($response);

        $this->assertDatabaseHas(
            'forum_thread_follows',
            [
                'thread_id' => $thread->getId(),
                'follower_id' => $user->getId(),
                'followed_on' => Carbon::now()->toDateTimeString()
            ]
        );
    }

    public function test_un_follow_thread()
    {
        $user = $this->fakeUserCloak();

        $thread = new Thread();
        $thread->randomize();
        $thread->setState(Thread::STATE_PUBLISHED);
        $thread->setAuthorId($user->getId());
        $thread->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($thread->getId());
        $post->setAuthorId($user->getId());
        $post->persist();

        $this->classBeingTested->follow($thread->getId(), $user->getId());

        $response = $this->classBeingTested->unFollow($thread->getId(), $user->getId());

        $this->assertTrue($response);

        $this->assertDatabaseMissing(
            'forum_thread_follows',
            [
                'thread_id' => $thread->getId(),
                'follower_id' => $user->getId(),
                'followed_on' => Carbon::now()->toDateTimeString()
            ]
        );
    }
}