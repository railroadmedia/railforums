<?php

namespace Railroad\Railforums\DataMappers;

use Railroad\Railforums\Entities\Post;
use Railroad\Railforums\Entities\PostReport;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railmap\Entity\Links\OneToOne;

/**
 * Class PostReportDataMapper
 *
 * @package Railroad\Railforums\DataMappers
 * @method PostReport|PostReport[] getWithQuery(callable $queryCallback, $forceArrayReturn = false)
 * @method PostReport|PostReport[] get($idOrIds)
 * @method array list(callable $queryCallback = null, $column = 'id')
 */
class PostReportDataMapper extends DataMapperBase
{
    public $table = 'forum_post_reports';
    public $with = ['reporter', 'post'];

    public function mapTo()
    {
        return [
            'id' => 'id',
            'postId' => 'post_id',
            'reporterId' => 'reporter_id',
            'reportedOn' => 'reported_on',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
        ];
    }

    public function links()
    {
        return [
            'reporter' => new OneToOne(UserCloak::class, 'reporterId', 'id', 'reporter'),
            'post' => new OneToOne(Post::class, 'postId', 'id', 'post'),
        ];
    }

    /**
     * @return PostReport
     */
    public function entity()
    {
        return new PostReport;
    }
}