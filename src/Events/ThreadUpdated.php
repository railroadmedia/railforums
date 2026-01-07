<?php

namespace Railroad\Railforums\Events;

class ThreadUpdated extends EventBase
{
    private $threadId;

    private $oldCategoryId;

    public function __construct($threadId, $userId, $oldCategoryId = null)
    {
        parent::__construct($userId);

        $this->threadId = $threadId;
        $this->oldCategoryId = $oldCategoryId;
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

    /**
     * @return int|null $oldCategoryId
     */
    public function getOldCategoryId() {
        return $this->oldCategoryId;
    }


}