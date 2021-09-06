<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Repositories\PostRepository;
use Railroad\Railforums\Services\ConfigService;

class PostEventListenerTest extends TestCase
{
    protected function setUp()
    {
        $this->setDefaultConnection('mysql');

        parent::setUp();
    }

    public function test_post_delete_event()
    {
        $user = $this->fakeUser();
        $this->actingAs($user);

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user['id']);

        /** @var array $postOne */
        $postOne = $this->fakePost($thread['id'], $user['id']);

        /** @var array $postTwo */
        $postTwo = $this->fakePost($thread['id'], $user['id']);

        $postRepository = $this->app->make(PostRepository::class);

        $deleteResult = $postRepository->delete($postTwo['id']);

        // assert thread not marked as soft delete
        $this->assertDatabaseHas(
            ConfigService::$tableThreads,
            [
                'id' => $thread['id'],
                'deleted_at' => null
            ]
        );

        $deleteResult = $postRepository->delete($postOne['id']);

        // assert thread marked as soft delete
        $this->assertDatabaseMissing(
            ConfigService::$tableThreads,
            [
                'id' => $thread['id'],
                'deleted_at' => null
            ]
        );
    }
}
