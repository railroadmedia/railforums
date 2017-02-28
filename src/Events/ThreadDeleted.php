<?php

namespace Railroad\Railforums\Events;

class ThreadDeleted
{
    private $threadId;

    public function __construct($threadId)
    {
        $this->threadId = $threadId;
    }

    /**
     * @return int
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * @param int $threadId
     */
    public function setThreadId($threadId)
    {
        $this->threadId = $threadId;
    }
}