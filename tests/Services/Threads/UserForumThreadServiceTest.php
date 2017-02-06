<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\Thread;
use Railroad\Railforums\Entities\ThreadRead;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railforums\Services\Threads\UserForumThreadService;
use Railroad\Railmap\Helpers\RailmapHelpers;

class UserForumThreadServiceTest extends TestCase
{
    /**
     * @var UserForumThreadService
     */
    private $classBeingTested;

    /**
     * @var UserCloak
     */
    private $currentUserCloak;

    public function setUp()
    {
        parent::setUp();

        $this->classBeingTested = app(UserForumThreadService::class);
        $this->currentUserCloak = $this->fakeCurrentUserCloak(UserCloak::PERMISSION_LEVEL_USER);
    }

    public function test_get_threads_1_page()
    {
        $categoryId = rand();

        $entities = [];

        for ($i = 0; $i < 3; $i++) {
            $user = $this->fakeUserCloak();

            $entity = new Thread();
            $entity->randomize();
            $entity->setState(Thread::STATE_PUBLISHED);
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

    public function test_get_threads_sorted_paginated()
    {
        $categoryId = rand();

        $entities = [];

        for ($i = 0; $i < 13; $i++) {
            $user = $this->fakeUserCloak();

            $entity = new Thread();
            $entity->randomize();
            $entity->setState(Thread::STATE_PUBLISHED);
            $entity->setCategoryId($categoryId);
            $entity->setAuthorId($user->getId());
            $entity->persist();

            $postCount = rand(1, 6);

            for ($x = 0; $x < $postCount; $x++) {
                $user = $this->fakeUserCloak();

                $post = new Post();
                $post->randomize();
                $post->setThreadId($entity->getId());
                $post->setAuthorId($user->getId());
                $post->setState(Post::STATE_PUBLISHED);
                $post->persist();
            }

            if ($this->faker->boolean()) {
                $threadRead = new ThreadRead();
                $threadRead->setThreadId($entity->getId());
                $threadRead->setReaderId($this->currentUserCloak->getId());
                $threadRead->setReadOn($entity->getLastPost()->getPublishedOn());
                $threadRead->persist();

                $entity->setIsRead(true);
            } else {
                $threadRead = new ThreadRead();
                $threadRead->setThreadId($entity->getId());
                $threadRead->setReaderId($this->currentUserCloak->getId());
                $threadRead->setReadOn(
                    Carbon::parse($entity->getLastPost()->getPublishedOn())->subDay()->toDateTimeString()
                );
                $threadRead->persist();

                $entity->setIsRead(false);
            }

            $entity->persist();

            $entities[] = $entity;
        }

        // Page 1
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

        // Page 2
        $expectedEntities = array_slice(
            RailmapHelpers::sortEntitiesByDateAttribute(
                $entities,
                'lastPostPublishedOn',
                'desc'
            ),
            5,
            5
        );

        $responseEntities = $this->classBeingTested->getThreads(
            5,
            2,
            $categoryId
        );

        $this->assertEquals($expectedEntities, $responseEntities);

        // Page 3
        $expectedEntities = array_slice(
            RailmapHelpers::sortEntitiesByDateAttribute(
                $entities,
                'lastPostPublishedOn',
                'desc'
            ),
            10,
            5
        );

        $responseEntities = $this->classBeingTested->getThreads(
            5,
            3,
            $categoryId
        );

        $this->assertEquals($expectedEntities, $responseEntities);
    }

    public function test_get_threads_sorted_paginated_none_exist()
    {
        $responseEntities = $this->classBeingTested->getThreads(
            5,
            1,
            rand()
        );

        $this->assertEmpty($responseEntities);
    }

    public function test_update_thread_read_now_new()
    {
        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId($this->currentUserCloak->getId());
        $post->persist();

        $this->classBeingTested->updateThreadRead($entity->getId(), $this->currentUserCloak->getId());

        $this->assertTrue($entity->getIsRead());

        $this->assertDatabaseHas(
            'forum_thread_reads',
            ['thread_id' => $entity->getId(), 'reader_id' => $this->currentUserCloak->getId()]
        );
    }

    public function test_update_thread_read_past_new()
    {
        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId($this->currentUserCloak->getId());
        $post->setPublishedOn(Carbon::now());
        $post->persist();

        $this->classBeingTested->updateThreadRead(
            $entity->getId(),
            $this->currentUserCloak->getId(),
            Carbon::now()->subDay()->toDateTimeString()
        );

        $this->assertFalse($entity->getIsRead());

        $this->assertDatabaseHas(
            'forum_thread_reads',
            ['thread_id' => $entity->getId(), 'reader_id' => $this->currentUserCloak->getId()]
        );
    }

    public function test_update_thread_read_update_to_new()
    {
        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId($this->currentUserCloak->getId());
        $post->setPublishedOn(Carbon::now());
        $post->persist();

        $this->classBeingTested->updateThreadRead(
            $entity->getId(),
            $this->currentUserCloak->getId(),
            Carbon::now()->subDay()->toDateTimeString()
        );

        $this->assertFalse($entity->getIsRead());

        $this->classBeingTested->updateThreadRead(
            $entity->getId(),
            $this->currentUserCloak->getId(),
            Carbon::now()
        );

        $this->assertTrue($entity->getIsRead());
    }

    public function test_update_thread_read_false_if_none()
    {
        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId($this->currentUserCloak->getId());
        $post->setPublishedOn(Carbon::now());
        $post->persist();

        $this->assertFalse($entity->getIsRead());
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

        $this->assertEquals(9, $responseCount);
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
        $post->setAuthorId($this->currentUserCloak->getId());
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

    public function test_update_thread_title_author_only()
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

        $newTitle = $this->faker->sentence();

        $response = $this->classBeingTested->updateThreadTitle($entity->getId(), $newTitle);

        $this->assertFalse($response);

        $this->assertDatabaseMissing(
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
}