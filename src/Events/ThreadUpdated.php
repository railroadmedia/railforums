<?php

namespace Railroad\Railforums\Events;

class ThreadUpdated extends EventBase
{
    private $threadId;

    public function __construct($threadId, $userId)
    {
        parent::__construct($userId);

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