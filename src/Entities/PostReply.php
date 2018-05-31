<?php

namespace Railroad\Railforums\Entities;

use Carbon\Carbon;
use Faker\Generator;
use Railroad\Railforums\DataMappers\PostReplyDataMapper;
use Railroad\Railmap\Entity\EntityBase;

/**
 * Class PostReply
 *
 */
class PostReply extends EntityBase
{
    /**
     * @var int
     */
    protected $childPostId;

    /**
     * @var int
     */
    protected $parentPostId;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->setOwningDataMapper(app(PostReplyDataMapper::class));
    }

    /**
     * @return int
     */
    public function getChildPostId()
    {
        return $this->childPostId;
    }

    /**
     * @param int $childPostId
     */
    public function setChildPostId($childPostId)
    {
        $this->childPostId = $childPostId;
    }

    /**
     * @return int
     */
    public function getParentPostId()
    {
        return $this->parentPostId;
    }

    /**
     * @param int $parentPostId
     */
    public function setParentPostId($parentPostId)
    {
        $this->parentPostId = $parentPostId;
    }

    public function randomize()
    {
        /** @var Generator $faker */
        $faker = app(Generator::class);

        $this->setChildPostId($faker->randomNumber());
        $this->setParentPostId($faker->randomNumber());
    }
}