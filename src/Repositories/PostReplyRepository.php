<?php

namespace Railroad\Railforums\Repositories;

use Illuminate\Database\Query\JoinClause;
use Railroad\Resora\Queries\CachedQuery;
use Railroad\Resora\Repositories\RepositoryBase;
use Railroad\Railforums\Services\ConfigService;

class PostReplyRepository extends RepositoryBase
{
    /**
     * @return CachedQuery|$this
     */
    protected function newQuery()
    {
        return (new CachedQuery($this->connection()))->from(ConfigService::$tablePostReplies);
    }

    protected function connection()
    {
        return app('db')->connection(ConfigService::$databaseConnectionName);
    }

    /**
     * Returns the posts that represent reply parents of the parameter post
     *
     * @param int $id
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPostReplyParents($id)
    {
        return $this->query()
            ->select(ConfigService::$tablePosts . ".*")
            ->leftJoin(ConfigService::$tablePosts,
                function (JoinClause $query) {
                    $query->on(
                        ConfigService::$tablePostReplies . '.parent_post_id',
                        '=',
                        ConfigService::$tablePosts . '.id'
                    );
                }
            )
            ->where(ConfigService::$tablePostReplies . '.child_post_id', $id)
            ->get();
    }

    /**
     * Performs a bulk insert
     *
     * @param array $payload
     */
    public function insert($payload)
    {
        $this->continueOrNewQuery()
            ->insert($payload);
    }
}
