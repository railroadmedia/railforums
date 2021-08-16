<?php

namespace Railroad\Railforums\Repositories;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Railroad\Railforums\Events\PostCreated;
use Railroad\Railforums\Events\PostDeleted;
use Railroad\Railforums\Events\PostUpdated;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Resora\Decorators\Decorator;
use Railroad\Resora\Queries\BaseQuery;
use Railroad\Resora\Queries\CachedQuery;

class PostRepository extends EventDispatchingRepository
{
    const STATE_PUBLISHED = 'published';
    const ACCESSIBLE_STATES = [self::STATE_PUBLISHED];
    const CHUNK_SIZE = 1000;

    public static $onlyMine = false;

    public function getCreateEvent($entity)
    {
        return new PostCreated($entity->id, auth()->id());
    }

    public function getReadEvent($entity)
    {
        return null;
    }

    public function getUpdateEvent($entity)
    {
        $id = is_object($entity) ? $entity->id : $entity;

        return new PostUpdated($id, auth()->id());
    }

    public function getDestroyEvent($entity)
    {
        return null;
    }

    public function getDeleteEvent($id)
    {
        return new PostDeleted($id, auth()->id());
    }

    /**
     * @return CachedQuery|$this
     */
    protected function newQuery()
    {
        return (new CachedQuery($this->connection()))->from(ConfigService::$tablePosts);
    }

    protected function decorate($results)
    {
        return Decorator::decorate($results, 'posts');
    }

    protected function connection()
    {
        return app('db')->connection(ConfigService::$databaseConnectionName);
    }

    protected function baseQuery()
    {
        return new BaseQuery($this->connection());
    }

    /**
     * Returns the posts count of the specified thread
     *
     * @param int $threadId
     *
     * @return int
     */
    public function getPostsCount($threadId)
    {
        $query = $this->query()
            ->selectRaw('COUNT(' . ConfigService::$tablePosts . '.id) as count')
            ->where(ConfigService::$tablePosts . '.thread_id', $threadId)
            ->whereIn(
                ConfigService::$tablePosts . '.state',
                self::ACCESSIBLE_STATES
            )
            ->whereNull(ConfigService::$tablePosts . '.deleted_at');

        if (self::$onlyMine) {
            $query->where('author_id', auth()->id());
        }

        return $query->value('count');
    }

    /**
     * Returns the posts and associated data
     *
     * @param int $amount
     * @param int $page
     * @param int $threadId
     *
     * @return Collection
     */
    public function getDecoratedPosts($amount, $page, $threadId, $orderByAndDirection = 'published_on')
    {
        $orderByDirection = substr($orderByAndDirection, 0, 1) !== '-' ? 'asc' : 'desc';

        $orderByColumn = trim($orderByAndDirection, '-');

        $query =
            $this->getDecoratedQuery()
                ->where(ConfigService::$tablePosts . '.thread_id', $threadId)
                ->whereIn(
                    ConfigService::$tablePosts . '.state',
                    self::ACCESSIBLE_STATES
                )
                ->limit($amount)
                ->skip($amount * ($page - 1));

        if ($orderByColumn == 'mine') {

            self::$onlyMine = true;

            $query->where('author_id', auth()->id());

            $orderByDirection = 'desc';
            $orderByColumn = 'published_on';
        }

        $query->orderBy($orderByColumn, $orderByDirection);

        return $query->get();
    }

    /**
     * Returns the posts and associated data
     *
     * @param array $ids
     *
     * @return Collection
     */
    public function getDecoratedPostsByIds($ids)
    {
        return $this->getDecoratedQuery()
            ->whereIn(ConfigService::$tablePosts . '.id', $ids)
            ->get();
    }

    /** Returns the posts of the specified thread
     *
     * @param $id
     * @param string $order
     * @return Collection
     */
    public function getAllPostIdsInThread($id, $order = 'asc')
    {
        return $this->baseQuery()
            ->from(ConfigService::$tablePosts)
            ->where('thread_id', $id)
            ->whereNull(ConfigService::$tablePosts . '.deleted_at')
            ->whereIn(
                ConfigService::$tablePosts . '.state',
                self::ACCESSIBLE_STATES
            )
            ->orderBy('published_on', $order)
            ->get();
    }

    /**
     * Returns a decorated query to retrive posts and associated data
     *
     * @return Builder
     */
    public function getDecoratedQuery()
    {
        $query =
            $this->query()
                ->select(ConfigService::$tablePosts . '.*')
                ->selectSub(
                    function (Builder $builder) {
                        return $builder->selectRaw('COUNT(*)')
                            ->from(ConfigService::$tablePostLikes)
                            ->limit(1)
                            ->whereRaw(
                                ConfigService::$tablePostLikes . '.post_id = ' . ConfigService::$tablePosts . '.id'
                            );
                    },
                    'like_count'
                )
                ->whereNull(ConfigService::$tablePosts . '.deleted_at');

        if (auth()->user()) {
            $query->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('COUNT(*) > 0')
                        ->from(ConfigService::$tablePostLikes)
                        ->limit(1)
                        ->whereRaw(
                            ConfigService::$tablePostLikes . '.post_id = ' . ConfigService::$tablePosts . '.id'
                        )
                        ->whereRaw(
                            ConfigService::$tablePostLikes . '.liker_id = ' . auth()->id() ?? 0
                        );
                },
                'is_liked_by_viewer'
            );
        }

        return $query;
    }

    /**
     * Get post data for search indexes
     */
    public function createSearchIndexes()
    {
        $query =
            $this->baseQuery()
                ->from(ConfigService::$tablePosts)
                ->join(
                    ConfigService::$tableThreads,
                    ConfigService::$tablePosts . '.thread_id',
                    '=',
                    ConfigService::$tableThreads . '.id'
                )
                ->select(ConfigService::$tablePosts . '.*')
                ->whereNull(ConfigService::$tablePosts . '.deleted_at')
                ->whereNull(ConfigService::$tableThreads . '.deleted_at')
                ->whereIn(
                    ConfigService::$tablePosts . '.state',
                    self::ACCESSIBLE_STATES
                )
                ->orderBy(ConfigService::$tablePosts . '.id');

        $posts = new Collection();
        $query->chunk(
            self::CHUNK_SIZE,
            function (Collection $postsData) use (
                &$posts
            ) {
                $posts = $posts->merge($postsData);
            }
        );

        return $posts;
    }

    /**
     * strips out blockquote tags with it's content, return the rest of the content, if not empty
     * if post content is composed just of blockquote tags, return the content without quoted post metadata
     */
    public function getFilteredPostContent($content)
    {
        return preg_replace(
            "~<blockquote(.*?)>(.*)</blockquote>~si",
            "",
            $content
        );
    }

    /**
     * Returns the posts count of the specified users
     *
     * @param array $userIds
     *
     * @return array
     */
    public function getUsersPostsCount(array $userIds)
    {
        $postsCount =
            $this->connection()
                ->table(ConfigService::$tablePosts)
                ->selectRaw('author_id')
                ->selectRaw('COUNT(' . ConfigService::$tablePosts . '.id) as count')
                ->whereIn('author_id', $userIds)
                ->groupBy('author_id')
                ->get()
                ->toArray();

        return array_combine(array_column($postsCount, 'author_id'), array_column($postsCount, 'count'));
    }

    /**
     * @param array $postIds
     * @return array|false|null
     */
    public function getPostsAuthorIds(array $postIds)
    {
        $postsAuthors =
            $this->connection()
                ->table(ConfigService::$tablePosts)
                ->whereIn('id', $postIds)
                ->get()
                ->toArray();

        return array_combine(array_column($postsAuthors, 'id'), array_column($postsAuthors, 'author_id'));
    }
}
