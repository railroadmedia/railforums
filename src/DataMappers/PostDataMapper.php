<?php

namespace Railroad\Railforums\DataMappers;

use Railroad\Railforums\Entities\Post;
use Railroad\Railmap\DataMapper\DatabaseDataMapperBase;

/**
 * Class PostDataMapper
 *
 * @package Railroad\Railforums\DataMappers
 * @method Post|Post[] getWithQuery(callable $queryCallback, $forceArrayReturn = false)
 * @method Post|Post[] get($idOrIds)
 */
class PostDataMapper extends DatabaseDataMapperBase
{
    protected $table = 'forum_posts';

    public static $viewingUserId = 0;

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

    /**
     * @return Post
     */
    public function entity()
    {
        return new Post();
    }
}