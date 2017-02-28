<?php

namespace Railroad\Railforums\Services\ThreadFollows;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railforums\DataMappers\ThreadFollowDataMapper;
use Railroad\Railforums\Entities\ThreadFollow;

class ThreadFollowService
{
    private $threadFollowDataMapper;
    private $threadDataMapper;

    public function __construct(
        ThreadFollowDataMapper $threadFollowDataMapper,
        ThreadDataMapper $threadDataMapper
    ) {
        $this->threadFollowDataMapper = $threadFollowDataMapper;
        $this->threadDataMapper = $threadDataMapper;
    }

    /**
     * @param $threadId
     * @param $followerId
     * @return bool
     */
    public function follow($threadId, $followerId)
    {
        if ($this->threadFollowDataMapper->exists(
            function (Builder $query) use ($threadId, $followerId) {
                return $query->where('thread_id', $threadId)->where('follower_id', $followerId);
            }
        )
        ) {
            return true;
        }

        $threadFollow = new ThreadFollow();
        $threadFollow->setThreadId($threadId);
        $threadFollow->setFollowerId($followerId);
        $threadFollow->setFollowedOn(Carbon::now()->toDateTimeString());
        $threadFollow->persist();

        $this->threadDataMapper->flushCache();

        return true;
    }

    /**
     * @param $threadId
     * @param $followerId
     * @return bool
     */
    public function unFollow($threadId, $followerId)
    {
        $existingFollows = $this->threadFollowDataMapper->getWithQuery(
            function (Builder $query) use ($threadId, $followerId) {
                return $query->where('thread_id', $threadId)->where('follower_id', $followerId);
            }
        );

        foreach ($existingFollows as $existingFollow) {
            $existingFollow->destroy();
        }

        $this->threadDataMapper->flushCache();

        return true;
    }

    /**
     * @param $threadId
     * @return array
     */
    public function getThreadFollowerIds($threadId)
    {
        return $this->threadFollowDataMapper->ignoreCache()->list(
            function (Builder $query) use ($threadId) {
                return $query->where('thread_id', $threadId);
            },
            'follower_id'
        );
    }
}