<?php

namespace Railroad\Railforums\DataMappers;

use Illuminate\Database\Query\JoinClause;
use Railroad\Railforums\Entities\Thread;
use Railroad\Railmap\DataMapper\DatabaseDataMapperBase;

class ThreadDataMapper extends DatabaseDataMapperBase
{
    protected $table = 'forum_threads';

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
            ]
        );
    }

    public function query()
    {
        return parent::query()->selectRaw(
            'forum_threads.*, ' .
            'forum_posts.published_on as last_post_published_on, ' .
            'forum_posts.author_id as last_post_user_id, ' .
            config('railforums.author_table_name') .
            '.' .
            config('railforums.author_table_display_name_column_name') .
            ' as last_post_user_display_name, ' .
            '(select count(*) from forum_posts where forum_posts.thread_id = forum_threads.id) as post_count'
        )->join(
            'forum_posts',
            function (JoinClause $query) {
                $query->on('forum_posts.thread_id', '=', 'forum_threads.id')
                    ->on(
                        'forum_posts.published_on',
                        '=',
                        $query->raw(
                            '(SELECT MAX(published_on) FROM forum_posts WHERE thread_id = forum_threads.id)'
                        )
                    );
            }
        )->join(
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
        );
    }

    /**
     * @return Thread
     */
    public function entity()
    {
        return new Thread();
    }
}