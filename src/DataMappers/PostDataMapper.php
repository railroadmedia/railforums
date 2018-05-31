<?php

namespace Railroad\Railforums\DataMappers;

use Illuminate\Database\Query\Builder;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\UserCloak;
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
            'versionSavedAt' => 'version_saved_at',
        ];
    }

    public function mapFrom()
    {
        return array_merge(
            $this->mapTo(),
            [
                'likeCount' => 'like_count',
                'isLikedByCurrentUser' => 'is_liked_by_viewer',
                'liker1Id' => 'liker_1_id',
                'liker1DisplayName' => 'liker_1_display_name',
                'liker2Id' => 'liker_2_id',
                'liker2DisplayName' => 'liker_2_display_name',
                'liker3Id' => 'liker_3_id',
                'liker4DisplayName' => 'liker_3_display_name',
            ]
        );
    }

    public function gettingQuery()
    {
        return parent::gettingQuery()
            ->select(['forum_posts.*'])
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('COUNT(*)')
                        ->from('forum_post_likes')
                        ->limit(1)
                        ->whereRaw('forum_post_likes.post_id = forum_posts.id');
                },
                'like_count'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('COUNT(*) > 0')
                        ->from('forum_post_likes')
                        ->limit(1)
                        ->whereRaw('forum_post_likes.post_id = forum_posts.id')
                        ->whereRaw('forum_post_likes.liker_id = ' . $this->userCloakDataMapper->getCurrentId());
                },
                'is_liked_by_viewer'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('liker_id')
                        ->from('forum_post_likes')
                        ->limit(1)
                        ->whereRaw('forum_post_likes.post_id = forum_posts.id')
                        ->orderBy('liked_on', 'desc');
                },
                'liker_1_id'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->select([config('railforums.author_table_display_name_column_name')])
                        ->from(config('railforums.author_table_name'))
                        ->limit(1)
                        ->whereRaw(config('railforums.author_table_name') . '.id = liker_1_id');
                },
                'liker_1_display_name'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('liker_id')
                        ->from('forum_post_likes')
                        ->limit(1)
                        ->skip(1)
                        ->whereRaw('forum_post_likes.post_id = forum_posts.id')
                        ->orderBy('liked_on', 'desc');
                },
                'liker_2_id'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->select([config('railforums.author_table_display_name_column_name')])
                        ->from(config('railforums.author_table_name'))
                        ->limit(1)
                        ->whereRaw(config('railforums.author_table_name') . '.id = liker_2_id');
                },
                'liker_2_display_name'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->selectRaw('liker_id')
                        ->from('forum_post_likes')
                        ->limit(1)
                        ->skip(2)
                        ->whereRaw('forum_post_likes.post_id = forum_posts.id')
                        ->orderBy('liked_on', 'desc');
                },
                'liker_3_id'
            )
            ->selectSub(
                function (Builder $builder) {
                    return $builder->select([config('railforums.author_table_display_name_column_name')])
                        ->from(config('railforums.author_table_name'))
                        ->limit(1)
                        ->whereRaw(config('railforums.author_table_name') . '.id = liker_3_id');
                },
                'liker_3_display_name'
            );
    }

    public function links()
    {
        // Don't even bother trying to figure out how the recent likes query works,
        // I just threw shit against the wall till it did, roughly inspired by:
        // 'greatest-n-per-group' solutions.

        return [
            //            'recentLikes' => new OneToMany(
            //                PostLike::class, 'id', 'postId', 'recentLikes', 'forum_post_likes.liked_on', 'desc',
            //                function (Builder $query) {
            //                    return parent::gettingQuery()->selectSub(
            //                        function (Builder $builder) {
            //                            return $builder->selectRaw('liker_id')
            //                                ->from('forum_post_likes')
            //                                ->limit(1)
            //                                ->whereRaw('forum_post_likes.post_id = forum_posts.id')
            //                                ->orderBy('liked_on', 'desc');
            //                        },
            //                        '1_liker'
            //                    )->selectSub(
            //                        function (Builder $builder) {
            //                            return $builder->selectRaw('liker_id')
            //                                ->from('forum_post_likes')
            //                                ->limit(1)
            //                                ->skip(1)
            //                                ->whereRaw('forum_post_likes.post_id = forum_posts.id')
            //                                ->orderBy('liked_on', 'desc');
            //                        },
            //                        '2_liker'
            //                    );
            //
            ////                    return parent::gettingQuery()->selectRaw(
            ////                        'forum_posts.*, ' .
            ////                        '(SELECT COUNT(id) FROM forum_post_likes WHERE forum_post_likes.post_id = forum_posts.id)' .
            ////                        ' AS like_count, ' .
            ////                        'CASE WHEN current_user_forum_post_like.id IS NULL THEN 0 ELSE 1 END AS is_liked_by_viewer'
            ////                    )->leftJoin(
            ////                        'forum_post_likes as current_user_forum_post_like',
            ////                        function (JoinClause $query) {
            ////                            $query->on(
            ////                                'current_user_forum_post_like.liker_id',
            ////                                '=',
            ////                                $query->raw($this->userCloakDataMapper->getCurrentId())
            ////                            )
            ////                                ->on(
            ////                                    'forum_posts.id',
            ////                                    '=',
            ////                                    'current_user_forum_post_like.post_id'
            ////                                );
            ////                        }
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