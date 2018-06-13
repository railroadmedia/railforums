<?php

namespace Railroad\Railforums\Repositories;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Railroad\Resora\Decorators\Decorator;
use Railroad\Resora\Queries\CachedQuery;
use Railroad\Resora\Repositories\RepositoryBase;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\Repositories\Traits\SoftDelete;

class ThreadRepository extends RepositoryBase
{
    const STATE_PUBLISHED = 'published';
    const ACESSIBLE_STATES = [self::STATE_PUBLISHED];

    use SoftDelete;

    /**
     * @var UserCloakDataMapper
     */
    protected $userCloakDataMapper;

    public function __construct()
    {
        $this->userCloakDataMapper = app(UserCloakDataMapper::class);
    }

    /**
     * @return CachedQuery|$this
     */
    protected function newQuery()
    {
        return (new CachedQuery($this->connection()))->from(ConfigService::$tableThreads);
    }

    protected function decorate($results)
    {
        return Decorator::decorate($results, 'threads');
    }

    protected function connection()
    {
        return app('db')->connection(ConfigService::$databaseConnectionName);
    }

    public function getDecoratedThreadsByIds($ids)
    {
        return $this->getDecoratedQuery()
            ->whereIn(ConfigService::$tableThreads . '.id', $ids)
            ->get();
    }

    public function getDecoratedThreads(
        $amount,
        $page,
        $categoryIds,
        $pinned = false,
        $followed = null
    ) {
        $query = $this->getDecoratedQuery();

        if ($followed === true) {
            $query->whereExists(
                function (Builder $builder) {
                    return $builder
                        ->selectRaw('*')
                        ->from(ConfigService::$tableThreadFollows)
                        ->limit(1)
                        ->where(
                            'follower_id',
                            $this->userCloakDataMapper->getCurrentId()
                        )
                        ->whereRaw(
                            ConfigService::$tableThreads . '.id = ' .
                            ConfigService::$tableThreadFollows . '.thread_id'
                        );
                }
            );
        }

        if (!empty($categoryIds)) {

            $query->whereIn('category_id', $categoryIds);
        }

        $query->limit($amount)
            ->skip($amount * ($page - 1))
            ->orderByRaw('last_post_published_on desc, id desc')
            ->whereIn(
                ConfigService::$tableThreads . '.state',
                self::ACESSIBLE_STATES
            )
            ->where('pinned', $pinned);

        return $query->get();
    }

    public function getThreadsCount(
        $categoryIds,
        $pinned = false,
        $followed = null
    ) {
        $query = $this->query()
            ->selectRaw('COUNT(' . ConfigService::$tableThreads . '.id) as count')
            ->whereIn(
                ConfigService::$tableThreads . '.state',
                self::ACESSIBLE_STATES
            )
            ->where(ConfigService::$tableThreads . '.pinned', $pinned);

        if (!empty($categoryIds)) {
            $query->whereIn(
                ConfigService::$tableThreads . '.category_id',
                $categoryIds
            );
        }

        if (is_bool($followed)) {

            $query->leftJoin(
                ConfigService::$tableThreadFollows,
                function (JoinClause $query) {
                    $query->on(
                        ConfigService::$tableThreadFollows . '.thread_id',
                        '=',
                        ConfigService::$tableThreads . '.id'
                    )->on(
                        ConfigService::$tableThreadFollows . '.follower_id',
                        '=',
                        $query->raw($this->userCloakDataMapper->getCurrentId())
                    );
                }
            );

            if ($followed === true) {

                $query->whereNotNull(ConfigService::$tableThreadFollows.'.id');

            } else if ($followed === false) {

                $query->whereNull(ConfigService::$tableThreadFollows . '.id');
            }
        }

        return $query->value('count');
    }

    public function getDecoratedQuery()
    {
        return $this->query()
            ->select(ConfigService::$tableThreads . '.*')
            ->selectSub(
                function (Builder $builder) {

                    return $builder
                        ->selectRaw('COUNT(*)')
                        ->from(ConfigService::$tablePosts)
                        ->whereRaw(
                            ConfigService::$tablePosts . '.thread_id = ' .
                            ConfigService::$tableThreads . '.id'
                        )
                        ->limit(1);
                },
                'post_count'
            )
            ->selectSub(
                function (Builder $builder) {

                    return $builder->select(['published_on'])
                        ->from(ConfigService::$tablePosts)
                        ->whereNull(ConfigService::$tableThreads . '.deleted_at')
                        ->whereRaw(
                            ConfigService::$tablePosts . '.thread_id = ' .
                            ConfigService::$tableThreads . '.id'
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
                        ->whereNull(ConfigService::$tableThreads . '.deleted_at')
                        ->whereRaw(
                            ConfigService::$tablePosts . '.thread_id = ' .
                            ConfigService::$tableThreads . '.id'
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
                        ->whereNull(ConfigService::$tableThreads . '.deleted_at')
                        ->whereRaw(
                            ConfigService::$tablePosts . '.thread_id = ' .
                            ConfigService::$tableThreads . '.id'
                        )
                        ->limit(1)
                        ->orderBy('published_on', 'desc');
                },
                'last_post_user_id'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->select([
                            config('railforums.author_table_display_name_column_name')
                        ])
                        ->from(config('railforums.author_table_name'))
                        ->whereRaw(config('railforums.author_table_name') . '.id = last_post_user_id')
                        ->limit(1);
                },
                'last_post_user_display_name'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder
                        ->selectRaw('COUNT(*) > 0')
                        ->from(ConfigService::$tableThreadReads)
                        ->where('reader_id', $this->userCloakDataMapper->getCurrentId())
                        ->whereRaw(
                            ConfigService::$tableThreadReads . '.thread_id = ' .
                            ConfigService::$tableThreads . '.id'
                        )
                        ->limit(1);
                },
                'is_read'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder
                        ->selectRaw('COUNT(*) > 0')
                        ->from(ConfigService::$tableThreadFollows)
                        ->where('follower_id', $this->userCloakDataMapper->getCurrentId())
                        ->whereRaw(
                            ConfigService::$tableThreadFollows . '.thread_id = ' .
                            ConfigService::$tableThreads . '.id'
                        )
                        ->limit(1);
                },
                'is_followed'
            );
    }
}
