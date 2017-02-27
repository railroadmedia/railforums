<?php

namespace Railroad\Railforums\Exceptions;

use Exception;

class CannotDeleteFirstPostInThread extends Exception
{
    protected $postId;

    public function __construct($postId, $message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->postId = $postId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->postId;
    }

    /**
     * @param int $postId
     */
    public function setId($postId)
    {
        $this->postId = $postId;
    }
}