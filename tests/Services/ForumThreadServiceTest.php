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
        $entities = [];

        $currentUserData = $this->fakeUser();

        for ($i = 0; $i < 10; $i++) {
            $entity = new Thread();
            $entity->randomize();
            $entity->setState(Thread::STATE_PUBLISHED);
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

            $entity->setLastPostPublishedOn($mostRecentPost->getPublishedOn());
            $entity->setLastPostUserDisplayName($mostRecentUserData['display_name']);
            $entity->setLastPostUserId($mostRecentUserData['id']);

            $entity->persist();

            $entities[] = $entity;
        }

        $entities = array_slice(
            RailmapHelpers::sortEntitiesByDateAttribute($entities, 'lastPostPublishedOn', 'desc'),
            0,
            5
        );

        $responseEntities = $this->classBeingTested->getThreadsSortedPaginated(
            5,
            1,
            $currentUserData['id']
        );

        $this->assertEquals($entities, $responseEntities);
    }

    public function test_thread_read_none_exist_false()
    {
        $currentUserData = $this->fakeUser();

        $entity = new Thread();
        $entity->randomize();
        $entity->persist();

        $userData = $this->fakeUser();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId($userData['id']);
        $post->persist();

        $entity->setLastPostPublishedOn($post->getPublishedOn());
        $entity->setLastPostUserDisplayName($userData['display_name']);
        $entity->setLastPostUserId($userData['id']);
        $entity->setIsRead(false);

        $responseEntity = $this->classBeingTested->getThread($entity->getId(), $currentUserData['id']);

        $this->assertEquals($entity, $responseEntity);
    }

    public function test_thread_read_exists_but_out_of_date_false()
    {
        $currentUserData = $this->fakeUser();

        $entity = new Thread();
        $entity->randomize();
        $entity->persist();

        $userData = $this->fakeUser();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId($userData['id']);
        $post->persist();

        $threadRead = new ThreadRead();
        $threadRead->setThreadId($entity->getId());
        $threadRead->setReaderId($currentUserData['id']);
        $threadRead->setReadOn(
            Carbon::parse($post->getPublishedOn())->subDay()->toDateTimeString()
        );
        $threadRead->persist();

        $entity->setLastPostPublishedOn($post->getPublishedOn());
        $entity->setLastPostUserDisplayName($userData['display_name']);
        $entity->setLastPostUserId($userData['id']);
        $entity->setIsRead(false);

        $responseEntity = $this->classBeingTested->getThread($entity->getId(), $currentUserData['id']);

        $this->assertEquals($entity, $responseEntity);
    }

    public function test_thread_read_exists_exact_date_true()
    {
        $currentUserData = $this->fakeUser();

        $entity = new Thread();
        $entity->randomize();
        $entity->persist();

        $userData = $this->fakeUser();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId($userData['id']);
        $post->persist();

        $threadRead = new ThreadRead();
        $threadRead->setThreadId($entity->getId());
        $threadRead->setReaderId($currentUserData['id']);
        $threadRead->setReadOn(
            Carbon::parse($post->getPublishedOn())->toDateTimeString()
        );
        $threadRead->persist();

        $entity->setLastPostPublishedOn($post->getPublishedOn());
        $entity->setLastPostUserDisplayName($userData['display_name']);
        $entity->setLastPostUserId($userData['id']);
        $entity->setPostCount(1);
        $entity->setIsRead(true);

        $responseEntity = $this->classBeingTested->getThread($entity->getId(), $currentUserData['id']);

        $this->assertEquals($entity, $responseEntity);
    }

    public function test_thread_read_exists_and_is_up_to_date_true()
    {
        $currentUserData = $this->fakeUser();

        $entity = new Thread();
        $entity->randomize();
        $entity->persist();

        $userData = $this->fakeUser();

        $post = new Post();
        $post->randomize();
        $post->setThreadId($entity->getId());
        $post->setAuthorId($userData['id']);
        $post->persist();

        $threadRead = new ThreadRead();
        $threadRead->setThreadId($entity->getId());
        $threadRead->setReaderId($currentUserData['id']);
        $threadRead->setReadOn(
            Carbon::parse($post->getPublishedOn())->addDay()->toDateTimeString()
        );
        $threadRead->persist();

        $entity->setLastPostPublishedOn($post->getPublishedOn());
        $entity->setLastPostUserDisplayName($userData['display_name']);
        $entity->setLastPostUserId($userData['id']);
        $entity->setPostCount(1);
        $entity->setIsRead(true);

        $responseEntity = $this->classBeingTested->getThread($entity->getId(), $currentUserData['id']);

        $this->assertEquals($entity, $responseEntity);
    }
}