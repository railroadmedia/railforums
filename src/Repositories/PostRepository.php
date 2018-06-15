<?php

namespace Railroad\Railforums\Repositories;

use Illuminate\Database\Query\Builder;
use Railroad\Resora\Decorators\Decorator;
use Railroad\Resora\Queries\CachedQuery;
use Railroad\Resora\Repositories\RepositoryBase;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\Repositories\Traits\SoftDelete;

class PostRepository extends RepositoryBase
{
    const STATE_PUBLISHED = 'published';
    const ACCESSIBLE_STATES = [self::STATE_PUBLISHED];

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
        return (new CachedQuery($this->connection()))->from(ConfigService::$tablePosts);
    }

    protected function decorate($results)
    {
        return Decorator::decorate($results, 'threads');
    }

    protected function connection()
    {
        return app('db')->connection(ConfigService::$databaseConnectionName);
    }

    public function getPostsCount($threadId)
    {
        return $this->query()
            ->selectRaw('COUNT(' . ConfigService::$tablePosts .'.id) as count')
            ->where(ConfigService::$tablePosts . '.thread_id', $threadId)
            ->whereIn(
                ConfigService::$tablePosts . '.state',
                self::ACCESSIBLE_STATES
            )
            ->value('count');
    }

    public function getDecoratedPosts($amount, $page, $threadId)
    {
        return $this->getDecoratedQuery()
            ->where(ConfigService::$tablePosts . '.thread_id', $threadId)
            ->whereIn(
                ConfigService::$tablePosts . '.state',
                self::ACCESSIBLE_STATES
            )
            ->limit($amount)
            ->skip($amount * ($page - 1))
            ->orderBy('published_on', 'asc')
            ->get();
    }

    public function getDecoratedPostsByIds($ids)
    {
        return $this->getDecoratedQuery()
            ->whereIn(ConfigService::$tablePosts . '.id', $ids)
            ->get();
    }

    public function getDecoratedQuery()
    {
        return $this->query()
            ->select(ConfigService::$tablePosts . '.*')
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('COUNT(*)')
                        ->from(ConfigService::$tablePostLikes)
                        ->limit(1)
                        ->whereRaw(
                            ConfigService::$tablePostLikes . '.post_id = ' .
                            ConfigService::$tablePosts . '.id'
                        );
                },
                'like_count'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('COUNT(*) > 0')
                        ->from(ConfigService::$tablePostLikes)
                        ->limit(1)
                        ->whereRaw(
                            ConfigService::$tablePostLikes . '.post_id = ' .
                            ConfigService::$tablePosts . '.id'
                        )
                        ->whereRaw(
                            ConfigService::$tablePostLikes . '.liker_id = ' .
                            $this->userCloakDataMapper->getCurrentId()
                        );
                },
                'is_liked_by_viewer'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('liker_id')
                        ->from(ConfigService::$tablePostLikes)
                        ->limit(1)
                        ->whereRaw(
                            ConfigService::$tablePostLikes . '.post_id = ' .
                            ConfigService::$tablePosts . '.id'
                        )
                        ->orderBy('liked_on', 'desc');
                },
                'liker_1_id'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->select([
                            config('railforums.author_table_display_name_column_name')
                        ])
                        ->from(config('railforums.author_table_name'))
                        ->limit(1)
                        ->whereRaw(
                            config('railforums.author_table_name') .
                            '.id = liker_1_id'
                        );
                },
                'liker_1_display_name'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('liker_id')
                        ->from(ConfigService::$tablePostLikes)
                        ->limit(1)
                        ->skip(1)
                        ->whereRaw(
                            ConfigService::$tablePostLikes . '.post_id = ' .
                            ConfigService::$tablePosts . '.id'
                        )
                        ->orderBy('liked_on', 'desc');
                },
                'liker_2_id'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->select([
                            config('railforums.author_table_display_name_column_name')
                        ])
                        ->from(config('railforums.author_table_name'))
                        ->limit(1)
                        ->whereRaw(
                            config('railforums.author_table_name') .
                            '.id = liker_2_id'
                        );
                },
                'liker_2_display_name'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('liker_id')
                        ->from(ConfigService::$tablePostLikes)
                        ->limit(1)
                        ->skip(2)
                        ->whereRaw(
                            ConfigService::$tablePostLikes . '.post_id = ' .
                            ConfigService::$tablePosts . '.id'
                        )
                        ->orderBy('liked_on', 'desc');
                },
                'liker_3_id'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->select([
                            config('railforums.author_table_display_name_column_name')
                        ])
                        ->from(config('railforums.author_table_name'))
                        ->limit(1)
                        ->whereRaw(
                            config('railforums.author_table_name') .
                            '.id = liker_3_id'
                        );
                },
                'liker_3_display_name'
            );
    }
}
