<?php

namespace Railroad\Railforums\EventListeners;

use Railroad\Railforums\Events\ThreadCreated;
use Railroad\Railforums\Events\ThreadDeleted;
use Railroad\Railforums\Repositories\CategoryRepository;
use Railroad\Railforums\Repositories\ThreadFollowRepository;
use Railroad\Railforums\Repositories\ThreadRepository;

class ThreadEventListener
{
    /**
     * @var ThreadFollowRepository
     */
    protected $threadFollowRepository;

    /**
     * @var ThreadRepository
     */
    protected $threadRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    public function __construct(
        ThreadFollowRepository $threadFollowRepository,
        ThreadRepository $threadRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->threadFollowRepository = $threadFollowRepository;
        $this->threadRepository = $threadRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function onCreated(ThreadCreated $threadCreated)
    {
        $thread = $this->threadRepository->read($threadCreated->getThreadId());

        $this->threadFollowRepository->follow($thread->id, $thread->author_id);
    }

    public function onDeleted(ThreadDeleted $threadDeleted)
    {
        $thread = $this->threadRepository->read($threadDeleted->getThreadId());

        $lastPostOnDiscussion = $this->categoryRepository->calculateLastPostId($thread['category_id']);
        $categoryThreadsCount = $this->threadRepository->getThreadsCount([$thread['category_id']]);
        $this->categoryRepository->update($thread['category_id'], [
            'last_post_id' => $lastPostOnDiscussion->post_id,
            'post_count' => $categoryThreadsCount,
        ]);
    }
}