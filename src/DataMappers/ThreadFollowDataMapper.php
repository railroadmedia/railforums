<?php

namespace Railroad\Railforums\DataMappers;

use Illuminate\Database\Query\Builder;
use Railroad\Railforums\Entities\ThreadFollow;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railmap\Entity\Links\OneToOne;

/**
 * Class ThreadFollowDataMapper
 *
 * @package Railroad\Railforums\DataMappers
 * @method ThreadFollow|ThreadFollow[] getWithQuery(callable $queryCallback, $forceArrayReturn = false)
 * @method ThreadFollow|ThreadFollow[] get($idOrIds)
 * @method array list(callable $queryCallback = null, $column = 'id')
 */
class ThreadFollowDataMapper extends DataMapperBase
{
    public $table = 'forum_thread_follows';
    public $with = ['follower'];

    public function mapTo()
    {
        return [
            'id' => 'id',
            'threadId' => 'thread_id',
            'followerId' => 'follower_id',
            'followedOn' => 'followed_on',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
        ];
    }

    public function links()
    {
        return [
            'follower' => new OneToOne(UserCloak::class, 'followerId', 'id', 'follower'),
        ];
    }

    /**
     * @return ThreadFollow()
     */
    public function entity()
    {
        return new ThreadFollow();
    }
}