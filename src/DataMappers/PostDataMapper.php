<?php

namespace Railroad\Railforums\DataMappers;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\PostLike;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railmap\Entity\Links\OneToMany;

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
    public $with = ['likes'];

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

    public function filter($query)
    {
        $permissionLevel = $this->currentUserCloak->getPermissionLevel();

        if ($permissionLevel == UserCloak::PERMISSION_LEVEL_ADMINISTRATOR ||
            $permissionLevel == UserCloak::PERMISSION_LEVEL_MODERATOR
        ) {
            return $query->whereIn('states', [Post::STATE_PUBLISHED, Post::STATE_HIDDEN]);
        }

        return $query->whereIn('states', [Post::STATE_PUBLISHED]);
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
                $query->on('current_user_forum_post_like.liker_id', '=', $this->currentUserCloak->getId())
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
            'likes' => new OneToMany(
                PostLike::class, 'id', 'postId', 'recentLikes', 'liked_on', 'desc',
                function (Builder $query) {
                    return $query->limit(3);
                }
            )
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
        return $this->count(
            function (Builder $query) use ($threadId) {
                return $query->where('thread_id', $threadId);
            }
        );
    }
}