<?php

namespace Railroad\Railforums\Events;

class PostDeleted extends EventBase
{
    private $postId;

    public function __construct($postId, $userId)
    {
        parent::__construct($userId);

        $this->postId = $postId;
    }

    /**
     * @return int
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @param int $postId
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;
    }
}