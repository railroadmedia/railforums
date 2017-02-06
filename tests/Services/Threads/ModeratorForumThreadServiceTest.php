<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\Thread;
use Railroad\Railforums\Entities\ThreadRead;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railforums\Services\Threads\ModeratorForumThreadService;
use Railroad\Railmap\Helpers\RailmapHelpers;

class ModeratorForumThreadServiceTest extends TestCase
{
    /**
     * @var ModeratorForumThreadService
     */
    private $classBeingTested;

    /**
     * @var UserCloak
     */
    private $currentUserCloak;

    public function setUp()
    {
        parent::setUp();

        $this->classBeingTested = app(ModeratorForumThreadService::class);
        $this->currentUserCloak = $this->fakeCurrentUserCloak(UserCloak::PERMISSION_LEVEL_MODERATOR);
    }

    public function test_get_threads_1_page_can_see_hidden()
    {
        $categoryId = rand();

        $entities = [];

        for ($i = 0; $i < 3; $i++) {
            $user = $this->fakeUserCloak();

            $entity = new Thread();
            $entity->randomize();
            $entity->setState(Thread::STATE_HIDDEN);
            $entity->setAuthorId($user->getId());
            $entity->setCategoryId($categoryId);
            $entity->persist();

            $postCount = rand(1, 6);
            $mostRecentPost = null;

            for ($x = 0; $x < $postCount; $x++) {
                $user = $this->fakeUserCloak();

                $post = new Post();
                $post->randomize();
                $post->setThreadId($entity->getId());
                $post->setAuthorId($user->getId());
                $post->persist();

                if (is_null($mostRecentPost) ||
                    Carbon::parse($post->getPublishedOn()) > $mostRecentPost->getPublishedOn()
                ) {
                    $mostRecentPost = $post;
                }
            }

            $threadRead = new ThreadRead();
            $threadRead->setThreadId($entity->getId());
            $threadRead->setReaderId($this->currentUserCloak->getId());

            if ($this->faker->boolean()) {
                $threadRead->setReadOn($mostRecentPost->getPublishedOn());

                $entity->setIsRead(true);
            } else {
                $threadRead->setReadOn(
                    Carbon::parse($mostRecentPost->getPublishedOn())->subDay()->toDateTimeString()
                );

                $entity->setIsRead(false);
            }

            $threadRead->persist();
            $entity->persist();

            $entities[] = $entity;
        }

        $expectedEntities = array_slice(
            RailmapHelpers::sortEntitiesByDateAttribute(
                $entities,
                'lastPostPublishedOn',
                'desc'
            ),
            0,
            5
        );

        $responseEntities = $this->classBeingTested->getThreads(
            5,
            1,
            $categoryId
        );

        $this->assertEquals($expectedEntities, $responseEntities);
    }

    public function test_get_thread_count()
    {
        $categoryId = rand();

        $entities = [];

        for ($i = 0; $i < 13; $i++) {
            $entity = new Thread();
            $entity->randomize();
            $entity->setState(Thread::STATE_PUBLISHED);
            $entity->setCategoryId($categoryId);
            $entity->persist();

            $user = $this->fakeUserCloak();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($entity->getId());
            $post->setAuthorId($user->getId());
            $post->persist();

            $entities[] = $entity;
        }

        $responseCount = $this->classBeingTested->getThreadCount($categoryId);

        $this->assertEquals(13, $responseCount);
    }

    public function test_get_thread_count_some_hidden()
    {
        $categoryId = rand();

        $entities = [];

        for ($i = 0; $i < 9; $i++) {
            $entity = new Thread();
            $entity->randomize();
            $entity->setState(Thread::STATE_PUBLISHED);
            $entity->setCategoryId($categoryId);
            $entity->persist();

            $user = $this->fakeUserCloak();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($entity->getId());
            $post->setAuthorId($user->getId());
            $post->persist();

            $entities[] = $entity;
        }

        for ($i = 0; $i < 6; $i++) {
            $entity = new Thread();
            $entity->randomize();
            $entity->setState(Thread::STATE_HIDDEN);
            $entity->setCategoryId($categoryId);
            $entity->persist();

            $entities[] = $entity;
        }
        $responseCount = $this->classBeingTested->getThreadCount($categoryId);

        $this->assertEquals(15, $responseCount);
    }

    public function test_get_thread_count_one_deleted()
    {
        $categoryId = rand();

        $entities = [];

        for ($i = 0; $i < 9; $i++) {
            $entity = new Thread();
            $entity->randomize();
            $entity->setState(Thread::STATE_PUBLISHED);
            $entity->setCategoryId($categoryId);
            $entity->persist();

            $user = $this->fakeUserCloak();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($entity->getId());
            $post->setAuthorId($user->getId());
            $post->persist();

            $entities[] = $entity;
        }

        $entities[0]->destroy();

        $responseCount = $this->classBeingTested->getThreadCount($categoryId);

        $this->assertEquals(8, $responseCount);
    }

    public function test_update_thread_title()
    {
        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->setAuthorId($this->currentUserCloak->getId());
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId(rand());
        $post->persist();

        $newTitle = $this->faker->sentence();

        $response = $this->classBeingTested->updateThreadTitle($entity->getId(), $newTitle);

        $this->assertTrue($response);

        $this->assertDatabaseHas(
            'forum_threads',
            [
                'id' => $entity->getId(),
                'title' => $newTitle,
                'slug' => RailmapHelpers::sanitizeForSlug($newTitle)
            ]
        );
    }

    public function test_create_thread()
    {
        Carbon::setTestNow(Carbon::now());

        $title = $this->faker->sentence();
        $firstPostContent = $this->faker->paragraph();
        $categoryId = $this->faker->randomNumber();
        $authorId = $this->faker->randomNumber();
        $pinned = false;
        $locked = true;

        $thread = $this->classBeingTested->createThread(
            $title,
            $firstPostContent,
            $categoryId,
            $authorId,
            $pinned,
            $locked
        );

        $this->assertDatabaseHas(
            'forum_threads',
            [
                'category_id' => $categoryId,
                'author_id' => $authorId,
                'title' => $title,
                'slug' => RailmapHelpers::sanitizeForSlug($title),
                'pinned' => $pinned,
                'locked' => $locked,
                'state' => Thread::STATE_PUBLISHED,
                'post_count' => 1,
                'last_post_id' => 1,
                'published_on' => Carbon::now(),
            ]
        );
    }

    public function test_set_thread_state_published()
    {
        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_HIDDEN);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId(rand());
        $post->persist();

        $entity->persist();

        $this->classBeingTested->setThreadAsPublished($entity->getId());

        $this->assertDatabaseHas(
            'forum_threads',
            ['id' => $entity->getId(), 'state' => Thread::STATE_PUBLISHED]
        );
    }

    public function test_set_thread_state_hidden()
    {
        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId(rand());
        $post->persist();

        $entity->persist();

        $this->classBeingTested->setThreadAsHidden($entity->getId());

        $this->assertDatabaseHas(
            'forum_threads',
            ['id' => $entity->getId(), 'state' => Thread::STATE_HIDDEN]
        );
    }

    public function test_set_thread_locked_true()
    {
        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->setLocked(false);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId(rand());
        $post->persist();

        $entity->persist();

        $this->classBeingTested->setThreadLocked($entity->getId(), true);

        $this->assertDatabaseHas(
            'forum_threads',
            ['id' => $entity->getId(), 'locked' => true]
        );
    }

    public function test_set_thread_locked_false()
    {
        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->setLocked(true);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId(rand());
        $post->persist();

        $entity->persist();

        $this->classBeingTested->setThreadLocked($entity->getId(), false);

        $this->assertDatabaseHas(
            'forum_threads',
            ['id' => $entity->getId(), 'locked' => false]
        );
    }

    public function test_set_thread_pinned_true()
    {
        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->setPinned(false);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId(rand());
        $post->persist();

        $entity->persist();

        $this->classBeingTested->setThreadPinned($entity->getId(), true);

        $this->assertDatabaseHas(
            'forum_threads',
            ['id' => $entity->getId(), 'pinned' => true]
        );
    }

    public function test_set_thread_pinned_false()
    {
        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->setPinned(true);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId(rand());
        $post->persist();

        $entity->persist();

        $this->classBeingTested->setThreadPinned($entity->getId(), false);

        $this->assertDatabaseHas(
            'forum_threads',
            ['id' => $entity->getId(), 'pinned' => false]
        );
    }

    public function test_destroy_thread()
    {
        Carbon::setTestNow(Carbon::now());

        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->setPinned(true);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId(rand());
        $post->persist();

        $entity->persist();

        $response = $this->classBeingTested->destroyThread($entity->getId());

        $this->assertTrue($response);

        $this->assertDatabaseHas(
            'forum_threads',
            [
                'id' => $post->getId(),
                'deleted_at' => Carbon::now()->toDateTimeString(),
            ]
        );
    }
}