<?php

namespace Railroad\Railforums\EventListeners;

use Railroad\Railforums\Events\ThreadCreated;
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

    public function __construct(
        ThreadFollowRepository $threadFollowRepository,
        ThreadRepository $threadRepository
    ) {
        $this->threadFollowRepository = $threadFollowRepository;
        $this->threadRepository = $threadRepository;
    }

    public function onCreated(ThreadCreated $threadCreated)
    {
        $thread = $this->threadRepository->read($threadCreated->getThreadId());

        $this->threadFollowRepository->follow($thread->id, $thread->author_id);
    }
}