<?php

namespace Railroad\Railforums\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Railroad\Resora\Decorators\Decorator;
use Railroad\Resora\Queries\BaseQuery;
use Railroad\Resora\Queries\CachedQuery;
use Railroad\Resora\Repositories\RepositoryBase;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railforums\Repositories\Traits\SoftDelete;

class PostRepository extends RepositoryBase
{
    const STATE_PUBLISHED = 'published';
    const ACCESSIBLE_STATES = [self::STATE_PUBLISHED];
    const CHUNK_SIZE = 100;

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
        return $this->query()
            ->selectRaw('COUNT(' . ConfigService::$tablePosts .'.id) as count')
            ->where(ConfigService::$tablePosts . '.thread_id', $threadId)
            ->whereIn(
                ConfigService::$tablePosts . '.state',
                self::ACCESSIBLE_STATES
            )
            ->value('count');
    }

    /**
     * Returns the posts and associated data
     *
     * @param int $amount
     * @param int $page
     * @param int $threadId
     *
     * @return \Illuminate\Support\Collection
     */
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

    /**
     * Returns the posts and associated data
     *
     * @param array $ids
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDecoratedPostsByIds($ids)
    {
        return $this->getDecoratedQuery()
            ->whereIn(ConfigService::$tablePosts . '.id', $ids)
            ->get();
    }

    /**
     * Returns a decorated query to retrive posts and associated data
     *
     * @return \Illuminate\Database\Query\Builder
     */
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

    /**
     * Performs a chunked table read and creates search index records with the fetched data
     */
    public function createSearchIndexes()
    {
        $authorsTable = config('railforums.author_table_name');
        $authorsTableKey = config('railforums.author_table_id_column_name');
        $displayNameColumn = config('railforums.author_table_display_name_column_name');

        $query = $this
            ->baseQuery()
            ->from(ConfigService::$tablePosts)
            ->select(ConfigService::$tablePosts . '.*')
            ->addSelect($authorsTable . '.' . $displayNameColumn)
            ->join(
                $authorsTable,
                $authorsTable . '.' . $authorsTableKey,
                '=',
                ConfigService::$tablePosts . '.author_id'
            )
            ->orderBy(ConfigService::$tablePosts . '.id');

        $instance = $this;

        $query->chunk(
            self::CHUNK_SIZE,
            function (Collection $postsData) use (
                $displayNameColumn,
                $instance
            ) {

                $chunk = [];
                $now = Carbon::now()->toDateTimeString();

                foreach ($postsData as $postData) {

                    $searchIndex = [
                        'high_value' => null,
                        'medium_value' => $postData->content,
                        'low_value' => $postData->{$displayNameColumn},
                        'thread_id' => $postData->thread_id,
                        'post_id' => $postData->id,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];

                    $chunk[] = $searchIndex;
                }

                $instance
                    ->baseQuery()
                    ->from(ConfigService::$tableSearchIndexes)
                    ->insert($chunk);
            }
        );
    }
}
