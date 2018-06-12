<?php

namespace Railroad\Railforums\Repositories;

use Railroad\Resora\Queries\CachedQuery;
use Railroad\Resora\Repositories\RepositoryBase;
use Railroad\Railforums\Services\ConfigService;

class PostRepository extends RepositoryBase
{
    /**
     * @return CachedQuery|$this
     */
    protected function newQuery()
    {
        return (new CachedQuery($this->connection()))->from(ConfigService::$tablePosts);
    }

    protected function connection()
    {
        return app('db')->connection(ConfigService::$databaseConnectionName);
    }
}
