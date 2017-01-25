<?php

namespace Railroad\Railforums\DataMappers;

use Railroad\Railforums\Entities\Post;
use Railroad\Railmap\DataMapper\DatabaseDataMapperBase;

class PostDataMapper extends DatabaseDataMapperBase
{
    protected $table = 'forum_posts';

    public function map()
    {
        return [
            'id' => 'id',
            'threadId' => 'thread_id',
            'authorId' => 'author_id',
            'promptingPostId' => 'prompting_post_id',
            'content' => 'content',
            'likes' => 'likes',
            'postedOn' => 'posted_on',
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