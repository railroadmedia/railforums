<?php

namespace Railroad\Railforums\Repositories;

use DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Railroad\Railforums\Events\ThreadCreated;
use Railroad\Railforums\Events\ThreadDeleted;
use Railroad\Railforums\Events\ThreadUpdated;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Resora\Decorators\Decorator;
use Railroad\Resora\Queries\BaseQuery;
use Railroad\Resora\Queries\CachedQuery;

class ThreadRepository extends EventDispatchingRepository
{
    const STATE_PUBLISHED = 'published';
    const ACCESSIBLE_STATES = [self::STATE_PUBLISHED];
    const CHUNK_SIZE = 1000;

    public static $onlyMine = false;

    public function getCreateEvent($entity)
    {
        return new ThreadCreated(
            $entity->id, auth()->id()
        );
    }

    public function getReadEvent($entity)
    {
        return null;
    }

    public function getUpdateEvent($entity)
    {
        $id = is_object($entity) ? $entity->id : $entity;

        return new ThreadUpdated(
            $id, auth()->id()
        );
    }

    public function getDestroyEvent($entity)
    {
        return null;
    }

    public function getDeleteEvent($id)
    {
        return new ThreadDeleted(
            $id, auth()->id()
        );
    }

    /**
     * @return CachedQuery|$this
     */
    protected function newQuery()
    {
        return (new CachedQuery($this->connection()))->from(ConfigService::$tableThreads);
    }

    protected function baseQuery()
    {
        return new BaseQuery($this->connection());
    }

    protected function decorate($results)
    {
        return Decorator::decorate($results, 'threads');
    }

    protected function connection()
    {
        return app('db')->connection(ConfigService::$databaseConnectionName);
    }

    /**
     * Returns the threads and associated data
     *
     * @param array $ids
     *
     * @return Collection
     */
    public function getDecoratedThreadsByIds($ids)
    {
        return $this->getDecoratedQuery()
            ->whereIn(ConfigService::$tableThreads . '.id', $ids)
            ->get();
    }

    /**
     * Returns the threads and associated data
     *
     * @param $amount
     * @param $page
     * @param $categoryIds
     * @param null $pinned
     * @param null $followed
     * @param $orderByAndDirection
     * @return Collection
     */
    public function getDecoratedThreads(
        $amount,
        $page,
        $categoryIds,
        $pinned = null,
        $followed = null,
        $orderByAndDirection = '-last_post_published_on'
    ) {
        $orderByDirection = substr($orderByAndDirection, 0, 1) !== '-' ? 'asc' : 'desc';

        $orderByColumn = trim($orderByAndDirection, '-');

        $query = $this->getDecoratedQuery();

        if ($followed === true) {
            $query->whereExists(function (Builder $builder) {
                return $builder->selectRaw('*')
                    ->from(ConfigService::$tableThreadFollows)
                    ->limit(1)
                    ->where(
                        'follower_id',
                        auth()->id()
                    )
                    ->whereRaw(
                        ConfigService::$tableThreads . '.id = ' . ConfigService::$tableThreadFollows . '.thread_id'
                    );
            });
        }

        if (!empty($categoryIds)) {

            $query->whereIn('category_id', $categoryIds);
        }

        $query->limit($amount)
            ->skip($amount * ($page - 1))
            ->whereIn(
                ConfigService::$tableThreads . '.state',
                self::ACCESSIBLE_STATES
            );

        if ($orderByColumn == 'mine') {
            $query->where('author_id', auth()->id());
            self::$onlyMine = true;
            $orderByDirection = 'desc';
            $orderByColumn = 'last_post_published_on';
        }

        if ($pinned) {
            $query->where('pinned', $pinned)
                ->orderByRaw('pinned desc, last_post_published_on desc, id desc');
        } else {
            $query->orderByRaw($orderByColumn . ' ' . $orderByDirection . ', id desc');
        }

        return $query->get();
    }

    /**
     * Returns the threads count matching specified parameter filters
     *
     * @param array $categoryIds
     * @param bool $pinned
     * @param bool $followed
     *
     * @return int
     */
    public function getThreadsCount(
        $categoryIds,
        $pinned = null,
        $followed = null
    ) {
        $query =
            $this->query()
                ->selectRaw('COUNT(' . ConfigService::$tableThreads . '.id) as count')
                ->whereIn(
                    ConfigService::$tableThreads . '.state',
                    self::ACCESSIBLE_STATES
                )
                ->whereNull(ConfigService::$tableThreads . '.deleted_at');

        if ($pinned !== null) {
            $query->where(ConfigService::$tableThreads . '.pinned', $pinned);
        }
        if (!empty($categoryIds)) {
            $query->whereIn(
                ConfigService::$tableThreads . '.category_id',
                $categoryIds
            );
        }

        if (is_bool($followed)) {

            $query->leftJoin(ConfigService::$tableThreadFollows, function (JoinClause $query) {
                $query->on(
                    ConfigService::$tableThreadFollows . '.thread_id',
                    '=',
                    ConfigService::$tableThreads . '.id'
                )
                    ->on(
                        ConfigService::$tableThreadFollows . '.follower_id',
                        '=',
                        $query->raw(
                            auth()->id()
                        )
                    );
            });

            if ($followed === true) {

                $query->whereNotNull(ConfigService::$tableThreadFollows . '.id');

            } else {
                if ($followed === false) {

                    $query->whereNull(ConfigService::$tableThreadFollows . '.id');
                }
            }
        }

        if (self::$onlyMine) {
            $query->where('author_id', auth()->id());
        }

        return $query->value('count');
    }

    /**
     * Returns a decorated query to retrive threads and associated data
     *
     * @return Builder
     */
    public function getDecoratedQuery()
    {
        return $this->query()
            ->select(
                ConfigService::$tableThreads . '.*',
                ConfigService::$tableCategories . '.slug as category_slug',
                ConfigService::$tableCategories . '.title as category'
            )
            ->join(
                ConfigService::$tableCategories,
                ConfigService::$tableThreads . '.category_id',
                '=',
                ConfigService::$tableCategories . '.id'
            )
            ->selectSub(
                function (Builder $builder) {

                    return $builder->selectRaw('COUNT(*)')
                        ->from(ConfigService::$tablePosts)
                        ->whereRaw(
                            ConfigService::$tablePosts . '.thread_id = ' . ConfigService::$tableThreads . '.id'
                        )
                        ->whereNull(ConfigService::$tablePosts . '.deleted_at')
                        ->limit(1);
                },
                'post_count'
            )
            ->selectSub(
                function (Builder $builder) {

                    return $builder->select(['published_on'])
                        ->from(ConfigService::$tablePosts)
                        ->whereNull(ConfigService::$tablePosts . '.deleted_at')
                        ->whereRaw(
                            ConfigService::$tablePosts . '.thread_id = ' . ConfigService::$tableThreads . '.id'
                        )
                        ->limit(1)
                        ->orderBy('published_on', 'desc');
                },
                'last_post_published_on'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->select(['id'])
                        ->from(ConfigService::$tablePosts)
                        ->whereNull(ConfigService::$tablePosts . '.deleted_at')
                        ->whereRaw(
                            ConfigService::$tablePosts . '.thread_id = ' . ConfigService::$tableThreads . '.id'
                        )
                        ->limit(1)
                        ->orderBy('published_on', 'desc');
                },
                'last_post_id'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->select(['author_id'])
                        ->from(ConfigService::$tablePosts)
                        ->whereNull(ConfigService::$tablePosts . '.deleted_at')
                        ->whereRaw(
                            ConfigService::$tablePosts . '.thread_id = ' . ConfigService::$tableThreads . '.id'
                        )
                        ->limit(1)
                        ->orderBy('published_on', 'desc');
                },
                'last_post_user_id'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('COUNT(*) > 0')
                        ->from(ConfigService::$tableThreadReads)
                        ->where(
                            'reader_id',
                            auth()->id()
                        )
                        ->whereRaw(
                            ConfigService::$tableThreadReads . '.thread_id = ' . ConfigService::$tableThreads . '.id'
                        )
                        ->limit(1);
                },
                'is_read'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('COUNT(*) > 0')
                        ->from(ConfigService::$tableThreadFollows)
                        ->where(
                            'follower_id',
                            auth()->id()
                        )
                        ->whereRaw(
                            ConfigService::$tableThreadFollows . '.thread_id = ' . ConfigService::$tableThreads . '.id'
                        )
                        ->limit(1);
                },
                'is_followed'
            )
            ->whereNull(ConfigService::$tableThreads . '.deleted_at');
    }

    /**
     * @param $string
     *
     * @return string
     */
    public static function sanitizeForSlug($string)
    {
        return strtolower(
            preg_replace(
                '/(\-)+/',
                '-',
                str_replace(' ', '-', preg_replace('/[^ \w]+/', '', str_replace('&', 'and', trim($string))))
            )
        );
    }

    /**
     * Return threads data for search indexes
     */
    public function createSearchIndexes()
    {
        $query =
            $this->baseQuery()
                ->from(ConfigService::$tableThreads)
                ->select(ConfigService::$tableThreads . '.*')
                ->whereNull(ConfigService::$tableThreads . '.deleted_at')
                ->orderBy(ConfigService::$tableThreads . '.id');

        $threads = new Collection();

        $query->chunk(self::CHUNK_SIZE, function (Collection $threadsData) use (
            &$threads
        ) {
            $threads = $threads->merge($threadsData);
        });

        return $threads;
    }

    /**
     * Returns the posts of the specified thread
     *
     * @param int $id
     *
     * @return Collection
     */
    public function getAllThreadIdsInCategory($id)
    {
        return $this->query()
            ->where('category_id', $id)
            ->whereNull(ConfigService::$tableThreads . '.deleted_at')
            ->whereIn(
                ConfigService::$tableThreads . '.state',
                self::ACCESSIBLE_STATES
            )
            ->orderBy('published_on', 'asc')
            ->get();
    }

    public function getThreadBySlug($slug)
    {
        return $this->query()
            ->where(ConfigService::$tableThreads . '.slug', $slug)
            ->get();
    }

    /**
     * @param $limit
     * @return Collection
     */
    public function getRecentThreads($limit)
    {
        DB::statement("SET SQL_MODE=''");

        return $this->query()
            ->join('forum_posts', 'forum_threads.id', '=', 'forum_posts.thread_id')
            ->whereNull('forum_posts.deleted_at')
            ->where('forum_posts.state', 'published')
            ->orderBy('forum_posts.created_at', 'desc')
            ->limit($limit)
            ->groupBy('forum_threads.id')
            ->get();
    }
}
