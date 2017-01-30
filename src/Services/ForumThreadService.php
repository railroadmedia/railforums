<?php

namespace Railroad\Railforums\Services;

use Illuminate\Database\Query\Builder;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railforums\Entities\Thread;

class ForumThreadService
{
    private $threadDataMapper;

    public function __construct(ThreadDataMapper $threadDataMapper)
    {
        $this->threadDataMapper = $threadDataMapper;
    }

    /**
     * @param $amount
     * @param $page
     * @param $viewingUserId
     * @param array $states
     * @param string $sortColumn
     * @param string $sortDirection
     * @return Thread|Thread[]
     */
    public function getThreadsSortedPaginated(
        $amount,
        $page,
        $viewingUserId,
        $states = [Thread::STATE_PUBLISHED],
        $sortColumn = 'last_post_published_on',
        $sortDirection = 'desc'
    ) {
        ThreadDataMapper::$viewingUserId = $viewingUserId;

        return $this->threadDataMapper->getWithQuery(
            function (Builder $builder) use ($amount, $page, $sortColumn, $sortDirection, $states) {
                return $builder->limit($amount)->skip($amount * ($page - 1))->orderBy(
                    $sortColumn,
                    $sortDirection
                )->whereIn('forum_threads.state', $states)->get();
            }
        );
    }

    /**
     * @param $id
     * @param bool $state
     * @return bool
     */
    public function setThreadState($id, $state)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread) &&
            array_search($state, [Thread::STATE_PUBLISHED, Thread::STATE_HIDDEN, Thread::STATE_DRAFT]) !==
            false
        ) {
            $thread->setState($state);
            $thread->persist();

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @param bool $state
     * @return bool
     */
    public function setThreadLockedState($id, bool $state)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread)) {
            $thread->setLocked($state);
            $thread->persist();

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @param bool $state
     * @return bool
     */
    public function setThreadPinnedState($id, bool $state)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread)) {
            $thread->setPinned($state);
            $thread->persist();

            return true;
        }

        return false;
    }

    public function destroyThread($id)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread)) {
            $thread->destroy();

            return true;
        }

        return false;
    }
}