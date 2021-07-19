<?php

namespace Railroad\Railforums\Repositories;

use Railroad\Railforums\Events\PostLiked;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Resora\Queries\CachedQuery;

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
        return new PostLiked($entity->post_id, auth()->id());
    }

    public function getReadEvent($entity)
    {
        return null;
    }

    public function getUpdateEvent($entity)
    {
        return null;
    }

    public function getDeleteEvent($id)
    {
        return null;
    }

    public function getDestroyEvent($entity)
    {
        return null;
    }

    /**
     * @param int $postId
     *
     * @return int
     */
    public function countPostLikes($postId)
    {
        return $this->query()
            ->selectRaw('COUNT(' . ConfigService::$tablePostLikes .'.id) as count')
            ->where(ConfigService::$tablePostLikes . '.post_id', $postId)
            ->value('count');
    }

    public function getLatestPostLike($postId)
    {
        return $this->query()
            ->where(ConfigService::$tablePostLikes . '.post_id', $postId)
            ->orderBy('id','desc')
            ->first();
    }

    public function getPostLikes($postId, $limit, $page)
    {
        return $this->query()
            ->where(ConfigService::$tablePostLikes . '.post_id', $postId)
            ->limit($limit)
            ->skip(($page - 1) * $limit)
            ->orderBy('id','desc')
            ->get();
    }
}
