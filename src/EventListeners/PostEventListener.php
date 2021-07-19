<?php

namespace Railroad\Railforums\EventListeners;

use Railroad\Railforums\Events\PostCreated;
use Railroad\Railforums\Repositories\PostRepository;
use Railroad\Railforums\Repositories\ThreadReadRepository;
use Railroad\Railforums\Repositories\ThreadRepository;
use Railroad\Railforums\Events\PostDeleted;

class PostEventListener
{
    protected $postRepository;

    protected $threadRepository;

    protected $threadReadRepository;

    public function __construct(
        PostRepository $postRepository,
        ThreadRepository $threadRepository,
        ThreadReadRepository $threadReadRepository
    ) {
        $this->postRepository = $postRepository;
        $this->threadRepository = $threadRepository;
        $this->threadReadRepository = $threadReadRepository;
    }

    public function onPostDeleted(PostDeleted $event)
    {
        $post = $this->postRepository->read($event->getPostId());

        $threadPostCount = $this->postRepository->getPostsCount($post->thread_id);

        if (!$threadPostCount) {
            $this->threadRepository->delete($post->thread_id);
        }
    }

    public function onPostCreated(PostCreated $event)
    {
        $post = $this->postRepository->read($event->getPostId());

        $this->threadReadRepository->markAsUnread($post['thread_id']);
    }
}
