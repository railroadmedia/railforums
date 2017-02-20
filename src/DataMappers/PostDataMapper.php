<?php

namespace Railroad\Railforums\DataMappers;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\PostLike;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railmap\Entity\Links\OneToMany;
use Railroad\Railmap\Entity\Links\OneToOne;

/**
 * Class PostDataMapper
 *
 * @package Railroad\Railforums\DataMappers
 * @method Post|Post[] getWithQuery(callable $queryCallback, $forceArrayReturn = false)
 * @method Post|Post[] get($idOrIds)
 */
class PostDataMapper extends DataMapperBase
{
    public $table = 'forum_posts';
    public $with = ['author'];
    public $cacheTime = 3600;

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
                'likeCount' => 'like_count',
                'isLikedByCurrentUser' => 'is_liked_by_viewer',
            ]
        );
    }

    public function gettingQuery()
    {
        return parent::gettingQuery()->selectRaw(
            'forum_posts.*, ' .
            '(SELECT COUNT(id) FROM forum_post_likes WHERE forum_post_likes.post_id = forum_posts.id)' .
            ' AS like_count, ' .
            'CASE WHEN current_user_forum_post_like.id IS NULL THEN 0 ELSE 1 END AS is_liked_by_viewer'
        )->leftJoin(
            'forum_post_likes as current_user_forum_post_like',
            function (JoinClause $query) {
                $query->on(
                    'current_user_forum_post_like.liker_id',
                    '=',
                    $query->raw($this->userCloakDataMapper->getCurrentId())
                )
                    ->on(
                        'forum_posts.id',
                        '=',
                        'current_user_forum_post_like.post_id'
                    );
            }
        );
    }

    public function links()
    {
        return [
            'recentLikes' => new OneToMany(
                PostLike::class, 'id', 'postId', 'recentLikes', 'forum_post_likes.liked_on', 'desc',
                function (Builder $query) {
                    return $query->selectRaw('forum_post_likes.*')->join(
                        'forum_post_likes as fpl2',
                        function (JoinClause $joinClause) {
                            return $joinClause->on(
                                'forum_post_likes.post_id',
                                '=',
                                'fpl2.post_id'
                            )->on('forum_post_likes.id', '<', 'fpl2.id')->on(
                                'fpl2.liker_id',
                                '!=',
                                $joinClause->raw($this->userCloakDataMapper->getCurrentId())
                            );
                        },
                        null,
                        null,
                        'left outer'
                    )->groupBy('forum_post_likes.id')
                        ->having($query->raw('COUNT(*)'), '<', 3)
                        ->orderBy('forum_post_likes.liked_on');
                }
            ),
//            'recentLikes' => new OneToMany(
//                PostLike::class, 'id', 'postId', 'recentLikes', 'fpl.liked_on', 'desc',
//                function (Builder $query) {
//                    return $query->selectRaw('forum_post_likes.*, (SELECT GROUP_CONCAT(fpl2.id) ' .
//                                             'FROM (SELECT * FROM forum_post_likes as fpl ' .
//                                             'WHERE fpl.post_id = forum_post_likes.post_id ORDER BY liked_on LIMIT 0, 3) as fpl2) as c_ids')
//
//                        ->rightJoin(
//                        'forum_post_likes as fpl',
//                        'fpl.post_id',
//                        '=',
//                        'forum_post_likes.post_id'
//                    );
//                }
//            ),
//            'recentLikes' => new OneToMany(
//                PostLike::class, 'id', 'postId', 'recentLikes', 'fpl.liked_on', 'desc',
//                function (Builder $query) {
//                    return $query->rightJoin(
//                        'forum_post_likes as fpl',
//                        function (JoinClause $joinClause) {
//                            $joinClause->on('fpl.post_id', '=', 'forum_post_likes.post_id')->orderBy(
//                                'fpl.liked_on'
//                            )->limit(3);
//                        }
//                    );
//                }
//            ),
            'author' => new OneToOne(UserCloak::class, 'authorId', 'id', 'author'),
            'promptingPost' => new OneToOne(Post::class, 'promptingPostId', 'id', 'promptingPost'),
        ];
    }

    /**
     * @return Post
     */
    public function entity()
    {
        return new Post();
    }

    public function countPostsInThread($threadId)
    {
        $cacheTime = $this->cacheTime;

        $this->cacheTime = null;

        $count = $this->count(
            function (Builder $query) use ($threadId) {
                return $query->where('thread_id', $threadId);
            }
        );

        $this->cacheTime = $cacheTime;

        return $count;
    }

    /**
     * @param $threadId
     * @return null|Post
     */
    public function getLatestPost($threadId)
    {
        return $this->getWithQuery(
            function (Builder $query) use ($threadId) {
                return $query->where('thread_id', $threadId)->orderBy('published_on', 'desc')
                    ->limit(1);
            }
        )[0] ?? null;
    }
}