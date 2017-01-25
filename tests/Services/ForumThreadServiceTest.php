<?php

namespace Tests;

use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\Thread;
use Railroad\Railforums\Services\ForumThreadService;

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

        for ($i = 0; $i < 6; $i++) {
            $entity = new Thread();
            $entity->randomize();
            $entity->persist();

            $replyCount = rand(0, 4);

            for ($x = 0; $x < $replyCount; $x++) {
                $post = new Post();
                $post->randomize();
                $post->setThreadId($entity->getId());
                $post->persist();
            }

            $entity->setLastPostTime($replyCount);

            $entities[] = $entity;
        }

        $this->classBeingTested->getThreadsSortedPaginated(3, 1, 'posted_on', 'desc');

    }
}