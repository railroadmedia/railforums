<?php

namespace Railroad\Railforums\DataMappers;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\Thread;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railmap\Entity\Links\OneToOne;

/**
 * Class ThreadDataMapper
 *
 * @package Railroad\Railforums\DataMappers
 * @method Thread|Thread[] getWithQuery(callable $queryCallback, $forceArrayReturn = false)
 * @method Thread|Thread[] get($idOrIds)
 */
class ThreadDataMapper extends DataMapperBase
{
    public $table = 'forum_threads';
    public $with = ['lastPost', 'author'];
    public $cacheTime = 3600;

    public function mapTo()
    {
        return [
            'id' => 'id',
            'categoryId' => 'category_id',
            'authorId' => 'author_id',
            'title' => 'title',
            'slug' => 'slug',
            'pinned' => 'pinned',
            'locked' => 'locked',
            'state' => 'state',
            'postCount' => 'post_count',
            'lastPostId' => 'last_post_id',
            'publishedOn' => 'published_on',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
            'deletedAt' => 'deleted_at',
            'versionMasterId' => 'version_master_id',
            'versionSavedAt' => 'version_saved_at'
        ];
    }

    public function mapFrom()
    {
        return array_merge(
            $this->mapTo(),
            [
                'postCount' => 'post_count',
                'isRead' => 'is_read',
                'isFollowed' => 'is_followed',
            ]
        );
    }

    /**
     * We need to override the count query because it doesn't work with the join group by.
     *
     * @param callable|null $queryCallback
     * @param string $column
     * @return int
     */
    public function count(callable $queryCallback = null, $column = '*')
    {
        $query = $this->gettingQuery();

        if (is_callable($queryCallback)) {
            $query = $queryCallback($query);
        }

        return $this->executeQueryOrGetCached(
            $query,
            function (Builder $query) use ($column) {
                $parentQuery = $query->newQuery();

                return $parentQuery->from($query->raw('(' . $query->toSql() . ') as counted_table'))
                    ->mergeBindings($query)
                    ->count();
            }
        );
    }

    public function gettingQuery()
    {
        return parent::gettingQuery()->selectRaw(
            'forum_threads.*, ' .
            'forum_posts.published_on as last_post_published_on, ' .
            'forum_posts.author_id as last_post_user_id, ' .
            config('railforums.author_table_name') .
            '.' .
            config('railforums.author_table_display_name_column_name') .
            ' as last_post_user_display_name, ' .
            'forum_thread_reads.id IS NOT NULL AND forum_thread_reads.read_on >= forum_posts.published_on as is_read, ' .
            'forum_thread_follows.id IS NOT NULL as is_followed'
        )->leftJoin(
            'forum_posts',
            function (JoinClause $query) {
                $query->on('forum_posts.thread_id', '=', 'forum_threads.id')
                    ->on(
                        'forum_posts.id',
                        '=',
                        'forum_threads.last_post_id'
                    );
            }
        )->leftJoin(
            config('railforums.author_table_name'),
            function (JoinClause $query) {
                $query->on(
                    'forum_posts.author_id',
                    '=',
                    config('railforums.author_table_name') .
                    '.' .
                    config('railforums.author_table_id_column_name')
                );
            }
        )->leftJoin(
            'forum_thread_reads',
            function (JoinClause $query) {
                $query->on(
                    'forum_thread_reads.thread_id',
                    '=',
                    'forum_threads.id'
                )->on(
                    'forum_thread_reads.reader_id',
                    '=',
                    $query->raw($this->userCloakDataMapper->getCurrentId())
                );
            }
        )->leftJoin(
            'forum_thread_follows',
            function (JoinClause $query) {
                $query->on(
                    'forum_thread_follows.thread_id',
                    '=',
                    'forum_threads.id'
                )->on(
                    'forum_thread_follows.follower_id',
                    '=',
                    $query->raw($this->userCloakDataMapper->getCurrentId())
                );
            }
        )->groupBy('forum_threads.id');
    }

    /**
     * @return Thread
     */
    public function entity()
    {
        return new Thread();
    }

    public function links()
    {
        return [
            'lastPost' => new OneToOne(Post::class, 'lastPostId', 'id', 'lastPost'),
            'author' => new OneToOne(UserCloak::class, 'authorId', 'id', 'author'),
        ];
    }
}