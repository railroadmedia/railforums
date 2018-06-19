<?php

namespace Railroad\Railforums\EventListeners;

use Railroad\Railforums\Repositories\PostRepository;
use Railroad\Railforums\Repositories\ThreadRepository;
use Railroad\Railforums\Events\PostDeleted;

class PostEventListener
{
    protected $postRepository;

    protected $threadRepository;

    public function __construct(
        PostRepository $postRepository,
        ThreadRepository $threadRepository
    ) {
        $this->postRepository = $postRepository;
        $this->threadRepository = $threadRepository;
    }

    public function onPostDeleted(PostDeleted $event)
    {
        $post = $this->postRepository->read($event->getPostId());

        $threadPostCount = $this->postRepository->getPostsCount($post->thread_id);

        if (!$threadPostCount) {
            $this->threadRepository->delete($post->thread_id);
        }
    }
}