<?php

namespace Railroad\Railforums\Entities;

use Faker\Generator;
use Railroad\Railforums\DataMappers\ThreadReadDataMapper;
use Railroad\Railmap\Entity\EntityBase;
use Railroad\Railmap\Entity\Properties\Timestamps;

class ThreadRead extends EntityBase
{
    use Timestamps;

    /**
     * @var int
     */
    protected $threadId;

    /**
     * @var int
     */
    protected $readerId;

    /**
     * @var string
     */
    protected $readOn;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setOwningDataMapper(app(ThreadReadDataMapper::class));
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
     * @return int
     */
    public function getReaderId()
    {
        return $this->readerId;
    }

    /**
     * @param int $readerId
     */
    public function setReaderId($readerId)
    {
        $this->readerId = $readerId;
    }

    /**
     * @return string
     */
    public function getReadOn()
    {
        return $this->readOn;
    }

    /**
     * @param string $readOn
     */
    public function setReadOn($readOn)
    {
        $this->readOn = $readOn;
    }

    public function randomize()
    {
        $faker = app(Generator::class);
    }
}