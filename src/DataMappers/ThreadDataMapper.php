<?php

namespace Railroad\Railforums\DataMappers;

use Illuminate\Database\Query\JoinClause;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\Thread;
use Railroad\Railmap\DataMapper\DatabaseDataMapperBase;
use Railroad\Railmap\Entity\Links\OneToOne;

/**
 * Class ThreadDataMapper
 *
 * @package Railroad\Railforums\DataMappers
 * @method Thread|Thread[] getWithQuery(callable $queryCallback, $forceArrayReturn = false)
 * @method Thread|Thread[] get($idOrIds)
 */
class ThreadDataMapper extends DatabaseDataMapperBase
{
    protected $table = 'forum_threads';
    protected $with = ['lastPost'];

    public static $viewingUserId = 0;

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
                'lastPostPublishedOn' => 'last_post_published_on',
                'lastPostUserDisplayName' => 'last_post_user_display_name',
                'lastPostUserId' => 'last_post_user_id',
                'postCount' => 'post_count',
                'isRead' => 'is_read',
            ]
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
            'forum_thread_reads.id IS NOT NULL AND forum_thread_reads.read_on >= forum_posts.published_on as is_read'
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
                    self::$viewingUserId
                );
            }
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
            'lastPost' => new OneToOne(Post::class, 'lastPostId', 'id', 'lastPost')
        ];
    }
}