<?php

namespace Railroad\Railforums\Repositories;

use Illuminate\Database\Query\Builder;
use Railroad\Resora\Decorators\Decorator;
use Railroad\Resora\Queries\CachedQuery;
use Railroad\Resora\Repositories\RepositoryBase;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;

class ThreadRepository extends RepositoryBase
{
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

    public function getDecoratedThreads($ids)
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
            )
            ->whereIn(ConfigService::$tableThreads . '.id', $ids)
            ->get();
    }
}
