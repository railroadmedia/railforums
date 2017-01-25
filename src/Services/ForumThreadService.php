<?php

namespace Railroad\Railforums\Services;

use Illuminate\Database\Query\Builder;
use Railroad\Railforums\DataMappers\ThreadDataMapper;

class ForumThreadService
{
    private $threadDataMapper;

    public function __construct(ThreadDataMapper $threadDataMapper)
    {
        $this->threadDataMapper = $threadDataMapper;
    }

    public function getThreadsSortedPaginated(
        $amount,
        $page,
        $sortColumn = 'posted_on',
        $sortDirection = 'desc'
    ) {
        $threads = $this->threadDataMapper->getWithQuery(
            function (Builder $builder) use ($amount, $page, $sortColumn, $sortDirection) {
                return $builder->limit($amount)->skip($amount * ($page - 1))->orderBy(
                    $sortColumn,
                    $sortDirection
                )->get();
            }
        );
    }
}