<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\Thread;
use Railroad\Railforums\Entities\ThreadRead;
use Railroad\Railforums\Services\ForumThreadService;
use Railroad\Railmap\Helpers\RailmapHelpers;

class ForumThreadServiceTest extends TestCase
{
    /**
     * @var ForumThreadService
     */
    private $classBeingTested;

    public function setUp()
    {
        parent::setUp();

        $this->classBeingTested = app(ForumThreadService::class);
    }

    public function test_get_threads_sorted_paginated()
    {
        $categoryId = rand();

        $entities = [];

        $currentUserData = $this->fakeUser();

        for ($i = 0; $i < 13; $i++) {
            $entity = new Thread();
            $entity->randomize();
            $entity->setState(Thread::STATE_PUBLISHED);
            $entity->setCategoryId($categoryId);
            $entity->persist();

            $postCount = rand(1, 6);
            $mostRecentPost = null;
            $mostRecentUserData = null;

            for ($x = 0; $x < $postCount; $x++) {
                $userData = $this->fakeUser();

                $post = new Post();
                $post->randomize();
                $post->setThreadId($entity->getId());
                $post->setAuthorId($userData['id']);
                $post->persist();

                if (is_null($mostRecentPost) ||
                    Carbon::parse($post->getPublishedOn()) > $mostRecentPost->getPublishedOn()
                ) {
                    $mostRecentPost = $post;
                    $mostRecentUserData = $userData;
                }
            }

            if ($this->faker->boolean()) {
                $threadRead = new ThreadRead();
                $threadRead->setThreadId($entity->getId());
                $threadRead->setReaderId($currentUserData['id']);
                $threadRead->setReadOn($mostRecentPost->getPublishedOn());
                $threadRead->persist();

                $entity->setIsRead(true);
            } else {
                $threadRead = new ThreadRead();
                $threadRead->setThreadId($entity->getId());
                $threadRead->setReaderId($currentUserData['id']);
                $threadRead->setReadOn(
                    Carbon::parse($mostRecentPost->getPublishedOn())->subDay()->toDateTimeString()
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

        $responseEntities = $this->classBeingTested->getThreadsSortedPaginated(
            5,
            1,
            $currentUserData['id'],
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

        $responseEntities = $this->classBeingTested->getThreadsSortedPaginated(
            5,
            2,
            $currentUserData['id'],
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

        $responseEntities = $this->classBeingTested->getThreadsSortedPaginated(
            5,
            3,
            $currentUserData['id'],
            $categoryId
        );

        $this->assertEquals($expectedEntities, $responseEntities);
    }

    public function test_get_threads_sorted_paginated_single_page()
    {
        $categoryId = rand();

        $entities = [];

        $currentUserData = $this->fakeUser();

        for ($i = 0; $i < 3; $i++) {
            $entity = new Thread();
            $entity->randomize();
            $entity->setState(Thread::STATE_PUBLISHED);
            $entity->setCategoryId($categoryId);
            $entity->persist();

            $postCount = rand(1, 6);
            $mostRecentPost = null;
            $mostRecentUserData = null;

            for ($x = 0; $x < $postCount; $x++) {
                $userData = $this->fakeUser();

                $post = new Post();
                $post->randomize();
                $post->setThreadId($entity->getId());
                $post->setAuthorId($userData['id']);
                $post->persist();

                if (is_null($mostRecentPost) ||
                    Carbon::parse($post->getPublishedOn()) > $mostRecentPost->getPublishedOn()
                ) {
                    $mostRecentPost = $post;
                    $mostRecentUserData = $userData;
                }
            }

            if ($this->faker->boolean()) {
                $threadRead = new ThreadRead();
                $threadRead->setThreadId($entity->getId());
                $threadRead->setReaderId($currentUserData['id']);
                $threadRead->setReadOn($mostRecentPost->getPublishedOn());
                $threadRead->persist();

                $entity->setIsRead(true);
            } else {
                $threadRead = new ThreadRead();
                $threadRead->setThreadId($entity->getId());
                $threadRead->setReaderId($currentUserData['id']);
                $threadRead->setReadOn(
                    Carbon::parse($mostRecentPost->getPublishedOn())->subDay()->toDateTimeString()
                );
                $threadRead->persist();

                $entity->setIsRead(false);
            }

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

        $responseEntities = $this->classBeingTested->getThreadsSortedPaginated(
            5,
            1,
            $currentUserData['id'],
            $categoryId
        );

        $this->assertEquals($expectedEntities, $responseEntities);
    }

    public function test_get_threads_sorted_paginated_none_exist()
    {
        $currentUserData = $this->fakeUser();

        $responseEntities = $this->classBeingTested->getThreadsSortedPaginated(
            5,
            1,
            $currentUserData['id'],
            rand()
        );

        $this->assertEmpty($responseEntities);
    }

    public function test_set_thread_state_published_shows_in_list()
    {
        $currentUserData = $this->fakeUser();

        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_DRAFT);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId($currentUserData['id']);
        $post->persist();

        $entity->persist();

        $this->classBeingTested->setThreadAsPublished($entity->getId());

        $this->assertDatabaseHas(
            'forum_threads',
            ['id' => $entity->getId(), 'state' => Thread::STATE_PUBLISHED]
        );

        $responseEntities = $this->classBeingTested->getThreadsSortedPaginated(
            1,
            1,
            $currentUserData['id'],
            $entity->getCategoryId()
        );

        $this->assertEquals([$entity], $responseEntities);
    }

    public function test_set_thread_state_draft_hide_from_list()
    {
        $currentUserData = $this->fakeUser();

        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId($currentUserData['id']);
        $post->persist();

        $entity->persist();

        $this->classBeingTested->setThreadAsDraft($entity->getId());

        $this->assertDatabaseHas(
            'forum_threads',
            ['id' => $entity->getId(), 'state' => Thread::STATE_DRAFT]
        );

        $responseEntities = $this->classBeingTested->getThreadsSortedPaginated(
            1,
            1,
            $currentUserData['id'],
            $entity->getCategoryId()
        );

        $this->assertEquals([], $responseEntities);
    }

    public function test_set_thread_state_hidden_hide_from_list()
    {
        $currentUserData = $this->fakeUser();

        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId($currentUserData['id']);
        $post->persist();

        $entity->persist();

        $this->classBeingTested->setThreadAsHidden($entity->getId());

        $this->assertDatabaseHas(
            'forum_threads',
            ['id' => $entity->getId(), 'state' => Thread::STATE_HIDDEN]
        );

        $responseEntities = $this->classBeingTested->getThreadsSortedPaginated(
            1,
            1,
            $currentUserData['id'],
            $entity->getCategoryId()
        );

        $this->assertEquals([], $responseEntities);
    }

    public function test_update_thread_read_now_new()
    {
        $currentUserData = $this->fakeUser();

        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId($currentUserData['id']);
        $post->persist();

        $this->classBeingTested->updateThreadRead($entity->getId(), $currentUserData['id']);

        $this->assertTrue($entity->getIsRead());

        $this->assertDatabaseHas(
            'forum_thread_reads',
            ['thread_id' => $entity->getId(), 'reader_id' => $currentUserData['id']]
        );
    }

    public function test_update_thread_read_past_new()
    {
        $currentUserData = $this->fakeUser();

        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId($currentUserData['id']);
        $post->setPublishedOn(Carbon::now());
        $post->persist();

        $this->classBeingTested->updateThreadRead(
            $entity->getId(),
            $currentUserData['id'],
            Carbon::now()->subDay()->toDateTimeString()
        );

        $this->assertFalse($entity->getIsRead());

        $this->assertDatabaseHas(
            'forum_thread_reads',
            ['thread_id' => $entity->getId(), 'reader_id' => $currentUserData['id']]
        );
    }

    public function test_update_thread_read_update_to_new()
    {
        $currentUserData = $this->fakeUser();

        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId($currentUserData['id']);
        $post->setPublishedOn(Carbon::now());
        $post->persist();

        $this->classBeingTested->updateThreadRead(
            $entity->getId(),
            $currentUserData['id'],
            Carbon::now()->subDay()->toDateTimeString()
        );

        $this->assertFalse($entity->getIsRead());

        $this->classBeingTested->updateThreadRead(
            $entity->getId(),
            $currentUserData['id'],
            Carbon::now()
        );

        $this->assertTrue($entity->getIsRead());
    }

    public function test_update_thread_read_false_if_none()
    {
        $currentUserData = $this->fakeUser();

        $entity = new Thread();
        $entity->randomize();
        $entity->setState(Thread::STATE_PUBLISHED);
        $entity->persist();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId($currentUserData['id']);
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

            $userData = $this->fakeUser();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($entity->getId());
            $post->setAuthorId($userData['id']);
            $post->persist();

            $entities[] = $entity;
        }

        $responseCount = $this->classBeingTested->getThreadCount($categoryId);

        $this->assertEquals(13, $responseCount);
    }

    public function test_get_thread_count_some_drafts()
    {
        $categoryId = rand();

        $entities = [];

        for ($i = 0; $i < 9; $i++) {
            $entity = new Thread();
            $entity->randomize();
            $entity->setState(Thread::STATE_PUBLISHED);
            $entity->setCategoryId($categoryId);
            $entity->persist();

            $userData = $this->fakeUser();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($entity->getId());
            $post->setAuthorId($userData['id']);
            $post->persist();

            $entities[] = $entity;
        }

        for ($i = 0; $i < 6; $i++) {
            $entity = new Thread();
            $entity->randomize();
            $entity->setState(Thread::STATE_DRAFT);
            $entity->setCategoryId($categoryId);
            $entity->persist();

            $userData = $this->fakeUser();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($entity->getId());
            $post->setAuthorId($userData['id']);
            $post->persist();

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

            $userData = $this->fakeUser();

            $post = new Post();
            $post->randomize();
            $post->setThreadId($entity->getId());
            $post->setAuthorId($userData['id']);
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
        $entity->persist();

        $userData = $this->fakeUser();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId($userData['id']);
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
}