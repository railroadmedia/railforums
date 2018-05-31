<?php

namespace Railroad\Railforums\DataMappers;

use Illuminate\Database\Query\Builder;
use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\PostReply;
use Railroad\Railmap\Entity\Links\OneToOne;

/**
 * Class PostReplyDataMapper
 *
 */
class PostReplyDataMapper extends DataMapperBase
{
    public $table = 'forum_post_replies';
    public $with = ['parent'];
    public $cacheTime = 3600;

    public function mapTo()
    {
        return [
            'id' => 'id',
            'childPostId' => 'child_post_id',
            'parentPostId' => 'parent_post_id',
        ];
    }

    public function links()
    {
        return ['parent' => new OneToOne(Post::class, 'parentPostId', 'id', 'parent')];
    }

    /**
     * @return PostReply()
     */
    public function entity()
    {
        return new PostReply();
    }

    /**
     * @param $postId
     * @return int
     */
    public function countPostReplies($childPostId)
    {
        return $this->count(
            function (Builder $query) use ($childPostId) {
                return $query->where('child_post_id', $childPostId);
            }
        );
    }
}