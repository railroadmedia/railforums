<?php

namespace Railroad\Railforums\Repositories;

use Railroad\Railforums\Services\ConfigService;
use Railroad\Resora\Queries\CachedQuery;

class UserSignaturesRepository extends EventDispatchingRepository
{
    /**
     * @return CachedQuery|$this
     */
    protected function newQuery()
    {
        return (new CachedQuery($this->connection()))->from(ConfigService::$tableUserSignatures);
    }

    protected function connection()
    {
        return app('db')->connection(ConfigService::$databaseConnectionName);
    }

    public function getUserSignature($userId = null)
    {
        return $this->query()
            ->select(ConfigService::$tableUserSignatures . '.*')
            ->where(ConfigService::$tableUserSignatures . '.user_id', $userId ?? auth()->id())
            ->first();
    }

    public function getCreateEvent($entity)
    {
        return null;
    }

    public function getReadEvent($entity)
    {
        return null;
    }

    public function getUpdateEvent($entity)
    {
        return null;
    }

    public function getDestroyEvent($entity)
    {
        return null;
    }

    public function getDeleteEvent($entity)
    {
        return null;
    }
}
