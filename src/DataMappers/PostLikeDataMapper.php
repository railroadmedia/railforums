<?php

namespace Railroad\Railforums\DataMappers;

use Railroad\Railforums\Entities\PostLike;
use Railroad\Railmap\DataMapper\DatabaseDataMapperBase;

class PostLikeDataMapper extends DatabaseDataMapperBase
{
    public $table = 'forum_post_likes';

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

    /**
     * @return PostLike()
     */
    public function entity()
    {
        return new PostLike();
    }
}