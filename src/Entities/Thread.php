<?php

namespace Railroad\Railforums\Entities;

use Carbon\Carbon;
use Faker\Generator;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railmap\Entity\EntityBase;
use Railroad\Railmap\Entity\Properties\SoftDelete;
use Railroad\Railmap\Entity\Properties\Timestamps;
use Railroad\Railmap\Entity\Properties\Versioned;

class Thread extends EntityBase
{
    use Timestamps, SoftDelete, Versioned;

    /**
     * @var int
     */
    protected $categoryId;

    /**
     * @var int
     */
    protected $authorId;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var bool
     */
    protected $pinned;

    /**
     * @var bool
     */
    protected $locked;

    /**
     * @var string
     */
    protected $postedOn;

    /**
     * @var int
     */
    protected $replyCount = 0;

    /**
     * @var string
     */
    protected $lastPostTime;

    /**
     * @var int
     */
    protected $lastPostUserId;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->setOwningDataMapper(app(ThreadDataMapper::class));
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param int $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @param int $authorId
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return bool
     */
    public function isPinned()
    {
        return $this->pinned;
    }

    /**
     * @param bool $pinned
     */
    public function setPinned($pinned)
    {
        $this->pinned = $pinned;
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * @param bool $locked
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    /**
     * @return string
     */
    public function getPostedOn()
    {
        return $this->postedOn;
    }

    /**
     * @param string $postedOn
     */
    public function setPostedOn($postedOn)
    {
        $this->postedOn = $postedOn;
    }

    /**
     * @return string|null
     */
    public function getLastPostTime()
    {
        return $this->lastPostTime;
    }

    /**
     * @param string|null $lastPostTime
     */
    public function setLastPostTime($lastPostTime)
    {
        $this->lastPostTime = $lastPostTime;
    }

    /**
     * @return int|null
     */
    public function getLastPostUserId()
    {
        return $this->lastPostUserId;
    }

    /**
     * @param int|null $lastPostUserId
     */
    public function setLastPostUserId($lastPostUserId)
    {
        $this->lastPostUserId = $lastPostUserId;
    }

    public function randomize()
    {
        /** @var Generator $faker */
        $faker = app(Generator::class);

        $this->setCategoryId($faker->randomNumber());
        $this->setAuthorId($faker->randomNumber());
        $this->setTitle($faker->sentence(4));
        $this->setSlug(strtolower(implode('-', $faker->words(4))));
        $this->setPinned($faker->boolean());
        $this->setLocked($faker->boolean());
        $this->setPostedOn(Carbon::instance($faker->dateTime)->toDateTimeString());
    }
}