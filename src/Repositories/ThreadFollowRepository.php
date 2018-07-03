<?php

namespace Railroad\Railforums\Repositories;

use Carbon\Carbon;
use Railroad\Resora\Queries\CachedQuery;
use Railroad\Resora\Repositories\RepositoryBase;
use Railroad\Railforums\Services\ConfigService;

class ThreadFollowRepository extends RepositoryBase
{
    /**
     * @return CachedQuery|$this
     */
    protected function newQuery()
    {
        return (new CachedQuery($this->connection()))->from(ConfigService::$tableThreadFollows);
    }

    protected function connection()
    {
        return app('db')->connection(ConfigService::$databaseConnectionName);
    }

    /**
     * Returns an array of the follower ids of specified thread
     *
     * @param int $threadId
     *
     * @return array
     */
    public function getThreadFollowerIds($threadId)
    {
        $collection = $this->newQuery()->where('thread_id', $threadId)->get();

        if (!$collection || $collection->isEmpty()) {
            return [];
        }

        return $collection->pluck('follower_id');
    }

    /**
     * Creates a new instance of ThreadFollow
     *
     * @param int $threadId
     * @param int $followerId
     *
     * @return \Railroad\Resora\Entities\Entity
     */
    public function follow($threadId, $followerId)
    {
        $now = Carbon::now()->toDateTimeString();

        return $this->create([
            'thread_id' => $threadId,
            'follower_id' => $followerId,
            'followed_on' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
