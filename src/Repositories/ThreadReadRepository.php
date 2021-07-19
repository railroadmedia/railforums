<?php

namespace Railroad\Railforums\Repositories;

use Carbon\Carbon;
use Railroad\Resora\Queries\CachedQuery;
use Railroad\Resora\Repositories\RepositoryBase;
use Railroad\Railforums\Services\ConfigService;

class ThreadReadRepository extends RepositoryBase
{
    /**
     * @return CachedQuery|$this
     */
    protected function newQuery()
    {
        return (new CachedQuery($this->connection()))->from(ConfigService::$tableThreadReads);
    }

    protected function connection()
    {
        return app('db')->connection(ConfigService::$databaseConnectionName);
    }

    /**
     * Creates a new instance of ThreadRead
     *
     * @param int $threadId
     * @param int $userId
     *
     * @return \Railroad\Resora\Entities\Entity
     */
    public function markRead($threadId, $userId)
    {
        $now = Carbon::now()->toDateTimeString();

        return $this->create([
            'thread_id' => $threadId,
            'reader_id' => $userId,
            'read_on' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function markAsUnread($threadId, $userId = null)
    {
       $query = $this->query()->where('thread_id','=', $threadId);
        if($userId){
            $query->where('reader_id','=', $userId);
        }


        return $query->delete();
    }
}
