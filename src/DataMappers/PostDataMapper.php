<?php

namespace Railroad\Railforums\DataMappers;

use Illuminate\Database\Query\JoinClause;
use Railroad\Railforums\Entities\Post;
use Railroad\Railmap\DataMapper\DatabaseDataMapperBase;

/**
 * Class PostDataMapper
 *
 * @package Railroad\Railforums\DataMappers
 * @method Post|Post[] getWithQuery(callable $queryCallback, $forceArrayReturn = false)
 * @method Post|Post[] get($idOrIds)
 */
class PostDataMapper extends DatabaseDataMapperBase
{
    public $table = 'forum_posts';

    public static $viewingUserId = 0;

    public function mapTo()
    {
        return [
            'id' => 'id',
            'threadId' => 'thread_id',
            'authorId' => 'author_id',
            'promptingPostId' => 'prompting_post_id',
            'content' => 'content',
            'state' => 'state',
            'publishedOn' => 'published_on',
            'editedOn' => 'edited_on',
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

            ]
        );
    }

    public function gettingQuery()
    {
        return parent::gettingQuery()->selectRaw(
            'forum_posts.*, ' .
            'forum_post_likes_liker_1.' .
            config('railforums.author_table_display_name_column_name') .
            ' as liker_1_display_name, ' .
            'forum_post_likes_liker_2.' .
            config('railforums.author_table_display_name_column_name') .
            ' as liker_2_display_name, ' .
            'forum_post_likes_liker_3.' .
            config('railforums.author_table_display_name_column_name') .
            ' as liker_3_display_name'
        )->leftJoin(
            'forum_post_likes as forum_post_likes_1',
            function (JoinClause $query) {
                $query->on('forum_posts.id', '=', 'forum_post_likes_1.post_id')
                    ->orderBy('forum_post_likes.liked_on', 'desc')->skip(0);
            }
        )->leftJoin(
            config('railforums.author_table_name') . ' as forum_post_likes_liker_1',
            function (JoinClause $query) {
                $query->on('forum_post_likes_liker_1.id', '=', 'forum_post_likes_1.liker_id');
            }
        )->leftJoin(
            'forum_post_likes as forum_post_likes_2',
            function (JoinClause $query) {
                $query->on('forum_posts.id', '=', 'forum_post_likes_2.post_id')
                    ->orderBy('forum_post_likes.liked_on', 'desc')->skip(1);
            }
        )->leftJoin(
            config('railforums.author_table_name') . ' as forum_post_likes_liker_2',
            function (JoinClause $query) {
                $query->on('forum_post_likes_liker_2.id', '=', 'forum_post_likes_2.liker_id');
            }
        )->leftJoin(
            'forum_post_likes as forum_post_likes_3',
            function (JoinClause $query) {
                $query->on('forum_posts.id', '=', 'forum_post_likes_3.post_id')
                    ->orderBy('forum_post_likes.liked_on', 'desc')->skip(2);
            }
        )->leftJoin(
            config('railforums.author_table_name') . ' as forum_post_likes_liker_3',
            function (JoinClause $query) {
                $query->on('forum_post_likes_liker_3.id', '=', 'forum_post_likes_3.liker_id');
            }
        );
    }

    /**
     * @return Post
     */
    public function entity()
    {
        return new Post();
    }
}