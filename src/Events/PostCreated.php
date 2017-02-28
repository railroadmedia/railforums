<?php

namespace Railroad\Railforums\Events;

class PostCreated
{
    private $postId;

    public function __construct($postId)
    {
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