<?php

namespace Railroad\Railforums\DataMappers;

use Railroad\Railforums\Entities\ThreadRead;
use Railroad\Railmap\DataMapper\DatabaseDataMapperBase;

class ThreadReadDataMapper extends DatabaseDataMapperBase
{
    protected $table = 'forum_thread_reads';

    public function mapTo()
    {
        return [
            'id' => 'id',
            'threadId' => '$thread_id',
            'readerId' => '$reader_id',
            'readOn' => '$read_on',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
        ];
    }

    /**
     * @return ThreadRead()
     */
    public function entity()
    {
        return new ThreadRead();
    }
}