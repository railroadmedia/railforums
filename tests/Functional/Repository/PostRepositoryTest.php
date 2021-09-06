<?php

namespace Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Railroad\Railforums\Events\PostCreated;
use Railroad\Railforums\Events\PostUpdated;
use Railroad\Railforums\Events\PostDeleted;
use Railroad\Railforums\Repositories\PostRepository;

class PostRepositoryTest extends TestCase
{
    protected function setUp()
    {
        $this->setDefaultConnection('mysql');

        parent::setUp();

        Event::fake();
    }

    public function test_post_create_event()
    {
        $user = $this->fakeUser();
        $this->actingAs($user);

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user['id']);

        $postData = [
            'content' => $this->faker->sentence(),
            'thread_id' => $thread['id'],
            'author_id' => $user['id'],
            'state' => PostRepository::STATE_PUBLISHED
        ];

        $postRepository = $this->app->make(PostRepository::class);
        $post = $postRepository->create($postData);

        // assert create event fired
        Event::assertDispatched(
            PostCreated::class,
            function($event) use ($post) {
                return $event->getPostId() == $post->id;
            }
        );
    }

    public function test_post_update_event()
    {
        $user = $this->fakeUser();
        $this->actingAs($user);

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user['id']);

        /** @var array $post */
        $post = $this->fakePost($thread['id'], $user['id']);

        $newContent = $this->faker->sentence();

        $postRepository = $this->app->make(PostRepository::class);
        $post = $postRepository->update($post['id'], ['content' => $newContent]);

        // assert update event fired
        Event::assertDispatched(
            PostUpdated::class,
            function($event) use ($post) {
                return $event->getPostId() == $post->id;
            }
        );
    }

    public function test_post_delete_event()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        /** @var array $thread */
        $thread = $this->fakeThread($category['id'], $user['id']);

        /** @var array $post */
        $post = $this->fakePost($thread['id'], $user['id']);

        $newContent = $this->faker->sentence();

        $postRepository = $this->app->make(PostRepository::class);
        $post = $postRepository->delete($post['id']);

        // assert soft delete event fired
        Event::assertDispatched(
            PostDeleted::class,
            function($event) use ($post) {
                return $event->getPostId() == $post;
            }
        );

        // assert update event was prevented from fireing
        Event::assertNotDispatched(PostUpdated::class);
    }
}
