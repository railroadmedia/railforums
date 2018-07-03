<?php

namespace Railroad\Railforums\Repositories;

use Railroad\Resora\Queries\CachedQuery;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Railforums\Events\PostLiked;

class PostLikeRepository extends EventDispatchingRepository
{
    /**
     * @return CachedQuery|$this
     */
    protected function newQuery()
    {
        return (new CachedQuery($this->connection()))->from(ConfigService::$tablePostLikes);
    }

    protected function connection()
    {
        return app('db')->connection(ConfigService::$databaseConnectionName);
    }

    public function getCreateEvent($entity)
    {
        return new PostLiked($entity->post_id, $this->userCloakDataMapper->getCurrentId());
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
}
