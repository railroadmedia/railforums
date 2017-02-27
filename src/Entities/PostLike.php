<?php

namespace Railroad\Railforums\Entities;

use Carbon\Carbon;
use Faker\Generator;
use Railroad\Railforums\DataMappers\PostLikeDataMapper;
use Railroad\Railmap\Entity\EntityBase;
use Railroad\Railmap\Entity\Properties\Timestamps;

/**
 * Class PostLike
 *
 * @package Railroad\Railforums\Entities
 * @method PostLikeDataMapper getOwningDataMapper()
 * @method UserCloak|null getLiker()
 * @method setLiker(UserCloak | null $lastPost)
 */
class PostLike extends EntityBase
{
    use Timestamps;

    /**
     * @var int
     */
    protected $postId;

    /**
     * @var int
     */
    protected $likerId;

    /**
     * @var string
     */
    protected $likedOn;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->setOwningDataMapper(app(PostLikeDataMapper::class));
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

    /**
     * @return string
     */
    public function getLikedOn()
    {
        return $this->likedOn;
    }

    /**
     * @param string $likedOn
     */
    public function setLikedOn($likedOn)
    {
        $this->likedOn = $likedOn;
    }

    public function randomize()
    {
        /** @var Generator $faker */
        $faker = app(Generator::class);

        $this->setPostId($faker->randomNumber());
        $this->setLikerId($faker->randomNumber());
        $this->setLikedOn(Carbon::instance($faker->dateTime)->toDateTimeString());
    }
}