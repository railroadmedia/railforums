<?php

namespace Railroad\Railforums\Services\PostReplies;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Railroad\Railforums\DataMappers\PostReplyDataMapper;

class PostReplyService
{
    /**
     * @var PostReplyDataMapper
     */
    protected $postReplyDataMapper;

    public function __construct(PostReplyDataMapper $postReplyDataMapper)
    {
        $this->postReplyDataMapper = $postReplyDataMapper;
    }

    /**
     * @param int $childPostId
     * @return array
     */
    public function getReplies($childPostId)
    {
        return $this->postReplyDataMapper->getWithQuery(
            function (Builder $builder) use ($childPostId) {
                return $builder
                    ->where('child_post_id', $childPostId);
            }
        );
    }
}
