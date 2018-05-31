<?php

namespace Railroad\Railforums\DataMappers;

use Illuminate\Database\Query\Builder;
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
            'publishedOn' => 'published_on',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
            'deletedAt' => 'deleted_at',
            'versionMasterId' => 'version_master_id',
            'versionSavedAt' => 'version_saved_at',
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
                'lastPostId' => 'last_post_id',
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
        return $this->gettingQuery()->count();
    }

    public function gettingQuery()
    {
        return parent::gettingQuery()
            ->select('forum_threads.*')
            ->selectSub(
                function (Builder $builder) {

                    return $builder->selectRaw('COUNT(*)')
                        ->from('forum_posts')
                        ->limit(1)
                        ->whereRaw('forum_posts.thread_id = forum_threads.id');
                },
                'post_count'
            )
            ->selectSub(
                function (Builder $builder) {

                    return $builder->select(['published_on'])
                        ->from('forum_posts')
                        ->whereNull($this->table . '.deleted_at')
                        ->whereNull($this->table . '.version_master_id')
                        ->limit(1)
                        ->whereRaw('forum_posts.thread_id = forum_threads.id')
                        ->orderBy('published_on', 'desc');
                },
                'last_post_published_on'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->select(['id'])
                        ->from('forum_posts')
                        ->whereNull($this->table . '.deleted_at')
                        ->whereNull($this->table . '.version_master_id')
                        ->limit(1)
                        ->whereRaw('forum_posts.thread_id = forum_threads.id')
                        ->orderBy('published_on', 'desc');
                },
                'last_post_id'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->select(['author_id'])
                        ->from('forum_posts')
                        ->whereNull($this->table . '.deleted_at')
                        ->whereNull($this->table . '.version_master_id')
                        ->limit(1)
                        ->whereRaw('forum_posts.thread_id = forum_threads.id')
                        ->orderBy('published_on', 'desc');
                },
                'last_post_user_id'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->select([config('railforums.author_table_display_name_column_name')])
                        ->from(config('railforums.author_table_name'))
                        ->limit(1)
                        ->whereRaw(config('railforums.author_table_name') . '.id = last_post_user_id');
                },
                'last_post_user_display_name'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder
                        ->selectRaw('COUNT(*) > 0')
                        ->from('forum_thread_reads')
                        ->limit(1)
                        ->where('reader_id', $this->userCloakDataMapper->getCurrentId())
                        ->whereRaw('forum_thread_reads.thread_id = forum_threads.id');
                },
                'is_read'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder
                        ->selectRaw('COUNT(*) > 0')
                        ->from('forum_thread_follows')
                        ->limit(1)
                        ->where('follower_id', $this->userCloakDataMapper->getCurrentId())
                        ->whereRaw('forum_thread_follows.thread_id = forum_threads.id');
                },
                'is_followed'
            );
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