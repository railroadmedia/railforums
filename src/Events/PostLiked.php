<?php

namespace Railroad\Railforums\Events;

class PostLiked
{
    private $postId;
    private $likerId;

    public function __construct($postId, $likerId)
    {
        $this->postId = $postId;
        $this->likerId = $likerId;
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