<?php

namespace Railroad\Railforums\Entities;

use Carbon\Carbon;
use Faker\Generator;
use Railroad\Railforums\DataMappers\ThreadFollowDataMapper;
use Railroad\Railmap\Entity\EntityBase;
use Railroad\Railmap\Entity\Properties\Timestamps;

/**
 * Class ThreadFollow
 *
 * @package Railroad\Railforums\Entities
 * @method ThreadFollowDataMapper getOwningDataMapper()
 * @method UserCloak|null getLiker()
 * @method setLiker(UserCloak | null $lastPost)
 */
class ThreadFollow extends EntityBase
{
    use Timestamps;

    /**
     * @var int
     */
    protected $threadId;

    /**
     * @var int
     */
    protected $followerId;

    /**
     * @var string
     */
    protected $followedOn;

    public function __construct()
    {
        $this->setOwningDataMapper(app(ThreadFollowDataMapper::class));
    }

    /**
     * @return int
     */
    public function getThreadId(): int
    {
        return $this->threadId;
    }

    /**
     * @param int $threadId
     */
    public function setThreadId(int $threadId)
    {
        $this->threadId = $threadId;
    }

    /**
     * @return int
     */
    public function getFollowerId(): int
    {
        return $this->followerId;
    }

    /**
     * @param int $followerId
     */
    public function setFollowerId(int $followerId)
    {
        $this->followerId = $followerId;
    }

    /**
     * @return string
     */
    public function getFollowedOn(): string
    {
        return $this->followedOn;
    }

    /**
     * @param string $followedOn
     */
    public function setFollowedOn(string $followedOn)
    {
        $this->followedOn = $followedOn;
    }

    public function randomize()
    {
        /** @var Generator $faker */
        $faker = app(Generator::class);

        $this->setThreadId($faker->randomNumber());
        $this->setFollowedOn($faker->randomNumber());
        $this->setFollowedOn(Carbon::instance($faker->dateTime)->toDateTimeString());
    }
}