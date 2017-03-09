<?php

namespace Railroad\Railforums\Services\Posts;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railforums\DataMappers\ThreadReadDataMapper;
use Railroad\Railforums\Entities\ThreadRead;

class ForumThreadReadService
{
    private $threadReadDataMapper;
    private $threadDataMapper;

    public function __construct(ThreadReadDataMapper $threadReadDataMapper, ThreadDataMapper $threadDataMapper)
    {
        $this->threadReadDataMapper = $threadReadDataMapper;
        $this->threadDataMapper = $threadDataMapper;
    }

    public function markThreadRead($threadId, $userCloakId)
    {
        $threadRead = $this->threadReadDataMapper->getWithQuery(function(Builder $query) use ($threadId, $userCloakId) {
            return $query->where('thread_id', $threadId)->where('reader_id', $userCloakId);
        })[0] ?? null;

        if (empty($threadRead)) {
            $threadRead = new ThreadRead();
        }

        $threadRead->setThreadId($threadId);
        $threadRead->setReaderId($userCloakId);
        $threadRead->setReadOn(Carbon::now()->toDateTimeString());
        $threadRead->persist();

        $this->threadDataMapper->flushCache();

        return $threadRead;
    }
}