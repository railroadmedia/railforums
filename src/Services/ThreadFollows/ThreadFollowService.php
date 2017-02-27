<?php

namespace Railroad\Railforums\Services\ThreadFollows;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Railroad\Railforums\DataMappers\ThreadFollowDataMapper;
use Railroad\Railforums\Entities\ThreadFollow;

class ThreadFollowService
{
    private $threadFollowDataMapper;

    public function __construct(ThreadFollowDataMapper $threadFollowDataMapper)
    {
        $this->threadFollowDataMapper = $threadFollowDataMapper;
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

        return true;
    }
}