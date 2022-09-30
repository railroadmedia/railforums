<?php

namespace Railroad\Railforums\EventListeners;

use Railroad\Railforums\Events\PostCreated;
use Railroad\Railforums\Repositories\CategoryRepository;
use Railroad\Railforums\Repositories\PostRepository;
use Railroad\Railforums\Repositories\ThreadReadRepository;
use Railroad\Railforums\Repositories\ThreadRepository;
use Railroad\Railforums\Events\PostDeleted;

class PostEventListener
{
    protected $postRepository;

    protected $threadRepository;

    protected $threadReadRepository;

    protected $categoryRepository;

    public function __construct(
        PostRepository $postRepository,
        ThreadRepository $threadRepository,
        ThreadReadRepository $threadReadRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->postRepository = $postRepository;
        $this->threadRepository = $threadRepository;
        $this->threadReadRepository = $threadReadRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function onPostDeleted(PostDeleted $event)
    {
        $post = $this->postRepository->read($event->getPostId());

        $threadPostCount = $this->postRepository->getPostsCount($post->thread_id);

        if (!$threadPostCount) {
            $this->threadRepository->delete($post->thread_id);
        }

        $lastPostOnThread = $this->threadRepository->calculateLastPostId($post->thread_id);
        $this->threadRepository->update($post->thread_id, [
            'last_post_id' => $lastPostOnThread->post_id,
            'post_count' => $threadPostCount,
        ]);

        $thread = $this->threadRepository->read($post->thread_id);
        $lastPostOnDiscussion = $this->categoryRepository->calculateLastPostId($thread['category_id']);
        $categoryThreadsCount = $this->threadRepository->getThreadsCount([$thread['category_id']]);
        $this->categoryRepository->update($thread['category_id'], [
            'last_post_id' => $lastPostOnDiscussion->post_id,
            'post_count' => $categoryThreadsCount,
        ]);
    }

    public function onPostCreated(PostCreated $event)
    {
        $post = $this->postRepository->read($event->getPostId());

        $thread = $this->threadRepository->read($post['thread_id']);

        $this->threadReadRepository->markAsUnread($post['thread_id']);

        $threadPostCount = $this->postRepository->getPostsCount($thread['id']);
        $this->threadRepository->update($thread['id'], [
            'last_post_id' => $post['id'],
            'post_count' => $threadPostCount + 1,
        ]);

        $categoryThreadsCount = $this->threadRepository->getThreadsCount([$thread['category_id']]);
        $this->categoryRepository->update($thread['category_id'], [
            'last_post_id' => $post['id'],
            'post_count' => $categoryThreadsCount + 1,
        ]);
    }
}
