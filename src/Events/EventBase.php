<?php

namespace Railroad\Railforums\Events;

class EventBase
{
    private $userId;
    private $brand;

    public function __construct($userId)
    {
        $this->userId = $userId;
        $this->brand = config('railforums.brand');
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     */
    public function setBrand(string $brand)
    {
        $this->brand = $brand;
    }
}