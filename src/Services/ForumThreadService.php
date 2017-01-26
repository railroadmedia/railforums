<?php

namespace Railroad\Railforums\Services;

use Illuminate\Database\Query\Builder;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railmap\Entity\EntityInterface;

class ForumThreadService
{
    private $threadDataMapper;
    private $viewingUserId;

    public function __construct(ThreadDataMapper $threadDataMapper)
    {
        $this->threadDataMapper = $threadDataMapper;
    }

    /**
     * @param $amount
     * @param $page
     * @param $viewingUserId
     * @param string $sortColumn
     * @param string $sortDirection
     * @return EntityInterface|EntityInterface[]
     */
    public function getThreadsSortedPaginated(
        $amount,
        $page,
        $viewingUserId,
        $sortColumn = 'last_post_published_on',
        $sortDirection = 'desc'
    ) {
        ThreadDataMapper::$viewingUserId = $viewingUserId;

        return $this->threadDataMapper->getWithQuery(
            function (Builder $builder) use ($amount, $page, $sortColumn, $sortDirection) {
                return $builder->limit($amount)->skip($amount * ($page - 1))->orderBy(
                    $sortColumn,
                    $sortDirection
                )->get();
            }
        );
    }

    /**
     * @param int $id
     * @param $viewingUserId
     * @return null|EntityInterface
     */
    public function getThread(int $id, $viewingUserId)
    {
        ThreadDataMapper::$viewingUserId = $viewingUserId;

        return $this->threadDataMapper->get($id);
    }

    /**
     * @return int
     */
    public function getViewingUserId()
    {
        return $this->viewingUserId;
    }

    /**
     * @param int $viewingUserId
     */
    public function setViewingUserId($viewingUserId)
    {
        $this->viewingUserId = $viewingUserId;
    }
}