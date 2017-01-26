<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\Thread;
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

        for ($i = 0; $i < 6; $i++) {
            $entity = new Thread();
            $entity->randomize();
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

            $entity->setLastPostPublishedOn($mostRecentPost->getPublishedOn());
            $entity->setLastPostUserDisplayName($mostRecentUserData['display_name']);
            $entity->setLastPostUserId($mostRecentUserData['id']);
            $entity->setPostCount($postCount);

            $entities[] = $entity;
        }

        $entities = array_slice(
            RailmapHelpers::sortEntitiesByDateAttribute($entities, 'publishedOn', 'desc'),
            0,
            3
        );

        $responseEntities = $this->classBeingTested->getThreadsSortedPaginated(3, 1, 'published_on', 'desc');

        $this->assertEquals($entities, $responseEntities);
    }
}