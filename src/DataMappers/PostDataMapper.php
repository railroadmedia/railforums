<?php

namespace Railroad\Railforums\DataMappers;

use Railroad\Railforums\Entities\Post;
use Railroad\Railmap\DataMapper\DatabaseDataMapperBase;

class PostDataMapper extends DatabaseDataMapperBase
{
    protected $table = 'forums_posts';

    public function map()
    {
        return [
            'id' => 'id',
            'categoryId' => 'category_id',
            'authorId' => 'author_id',
            'title' => 'title',
            'slug' => 'slug',
            'pinned' => 'pinned',
            'locked' => 'locked',
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