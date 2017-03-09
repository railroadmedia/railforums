<?php

namespace Railroad\Railforums\Events;

class PostUnLiked extends EventBase
{
    private $postId;
    private $likerId;

    public function __construct($postId, $userId)
    {
        parent::__construct($userId);

        $this->postId = $postId;
        $this->likerId = $userId;
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

    /**
     * @return int
     */
    public function getLikerId()
    {
        return $this->likerId;
    }

    /**
     * @param int $likerId
     */
    public function setLikerId($likerId)
    {
        $this->likerId = $likerId;
    }
}