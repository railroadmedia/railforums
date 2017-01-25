<?php

namespace Railroad\Railforums\DataMappers;

use Railroad\Railforums\Entities\Thread;
use Railroad\Railmap\DataMapper\DatabaseDataMapperBase;

class ThreadDataMapper extends DatabaseDataMapperBase
{
    protected $table = 'forum_threads';

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
     * @return Thread
     */
    public function entity()
    {
        return new Thread();
    }
}