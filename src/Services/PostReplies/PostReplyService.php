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
        // $this->postReplyDataMapper->with = ['parent'];

        // echo "\n\n $$$ getReplies\n\n";

        return $this->postReplyDataMapper->getWithQuery(
            function (Builder $builder) use ($childPostId) {
                // echo "\n\n $$$ getReplies builder\n\n";
                return $builder
                    ->where('child_post_id', $childPostId);
            }
        );
    }
}
