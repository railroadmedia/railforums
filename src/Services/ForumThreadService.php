<?php

namespace Railroad\Railforums\Services;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railforums\DataMappers\ThreadReadDataMapper;
use Railroad\Railforums\Entities\Thread;
use Railroad\Railforums\Entities\ThreadRead;
use Railroad\Railmap\Helpers\RailmapHelpers;

class ForumThreadService
{
    private $threadDataMapper;
    private $threadReadDataMapper;

    public function __construct(
        ThreadDataMapper $threadDataMapper,
        ThreadReadDataMapper $threadReadDataMapper
    ) {
        $this->threadDataMapper = $threadDataMapper;
        $this->threadReadDataMapper = $threadReadDataMapper;
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
     * @return bool
     */
    public function setThreadAsDraft($id)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread)) {
            $thread->setState(Thread::STATE_DRAFT);
            $thread->persist();

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function setThreadAsPublished($id)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread)) {
            $thread->setState(Thread::STATE_PUBLISHED);
            $thread->persist();

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function setThreadAsHidden($id)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread)) {
            $thread->setState(Thread::STATE_HIDDEN);
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
    public function setThreadLocked($id, bool $state)
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
    public function setThreadPinned($id, bool $state)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread)) {
            $thread->setPinned($state);
            $thread->persist();

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function destroyThread($id)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread)) {
            $thread->destroy();

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @param $readerId
     * @param null $dateTimeString
     * @return bool
     */
    public function updateThreadRead($id, $readerId, $dateTimeString = null)
    {
        if (is_null($dateTimeString)) {
            $dateTime = Carbon::now();
        } else {
            $dateTime = Carbon::parse($dateTimeString);
        }

        $threadRead = $this->threadReadDataMapper->get($id);

        if (empty($threadRead)) {
            $threadRead = new ThreadRead();
            $threadRead->setThreadId($id);
            $threadRead->setReaderId($readerId);
        }

        $threadRead->setReadOn($dateTime->toDateTimeString());
        $threadRead->persist();

        return true;
    }

    /**
     * @param array $states
     * @return int
     */
    public function getThreadCount($states = [Thread::STATE_PUBLISHED])
    {
        return $this->threadDataMapper->count(
            function (Builder $builder) use ($states) {
                return $builder->whereIn('forum_threads.state', $states);
            }
        );
    }

    /**
     * @param $id
     * @param string $title
     * @return bool
     */
    public function updateThreadTitle($id, $title)
    {
        $thread = $this->threadDataMapper->get($id);

        if (!empty($thread)) {
            $thread->setTitle($title);
            $thread->setSlug(RailmapHelpers::sanitizeForSlug($title));
            $thread->persist();

            return true;
        }

        return false;
    }
}