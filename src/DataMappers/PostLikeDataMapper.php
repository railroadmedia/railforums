<?php

namespace Railroad\Railforums\DataMappers;

use Illuminate\Database\Query\Builder;
use Railroad\Railforums\Entities\PostLike;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railmap\DataMapper\DatabaseDataMapperBase;
use Railroad\Railmap\Entity\Links\OneToOne;

class PostLikeDataMapper extends DataMapperBase
{
    public $table = 'forum_post_likes';
    public $with = ['liker'];

    public function mapTo()
    {
        return [
            'id' => 'id',
            'postId' => 'post_id',
            'likerId' => 'liker_id',
            'likedOn' => 'liked_on',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
        ];
    }

    public function links()
    {
        return ['liker' => new OneToOne(UserCloak::class, 'likerId', 'id', 'liker')];
    }

    /**
     * @return PostLike()
     */
    public function entity()
    {
        return new PostLike();
    }

    /**
     * @param $postId
     * @return int
     */
    public function countPostLikes($postId)
    {
        return $this->count(
            function (Builder $query) use ($postId) {
                return $query->where('post_id', $postId);
            }
        );
    }
}