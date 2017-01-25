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
     * @var string|null
     */
    protected $publishedOn;

    /**
     * @var string
     */
    protected $lastPostPublishedOn;

    /**
     * @var string
     */
    protected $lastPostUserDisplayName;

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
    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    /**
     * @param int $categoryId
     */
    public function setCategoryId(int $categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    /**
     * @param int $authorId
     */
    public function setAuthorId(int $authorId)
    {
        $this->authorId = $authorId;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return bool
     */
    public function isPinned(): bool
    {
        return $this->pinned;
    }

    /**
     * @param bool $pinned
     */
    public function setPinned(bool $pinned)
    {
        $this->pinned = $pinned;
    }

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * @param bool $locked
     */
    public function setLocked(bool $locked)
    {
        $this->locked = $locked;
    }

    /**
     * @return null|string
     */
    public function getPublishedOn()
    {
        return $this->publishedOn;
    }

    /**
     * @param null|string $publishedOn
     */
    public function setPublishedOn($publishedOn)
    {
        $this->publishedOn = $publishedOn;
    }

    /**
     * @return string
     */
    public function getLastPostPublishedOn(): string
    {
        return $this->lastPostPublishedOn;
    }

    /**
     * @param string $lastPostPublishedOn
     */
    public function setLastPostPublishedOn(string $lastPostPublishedOn)
    {
        $this->lastPostPublishedOn = $lastPostPublishedOn;
    }

    /**
     * @return string
     */
    public function getLastPostUserDisplayName(): string
    {
        return $this->lastPostUserDisplayName;
    }

    /**
     * @param string $lastPostUserDisplayName
     */
    public function setLastPostUserDisplayName(string $lastPostUserDisplayName)
    {
        $this->lastPostUserDisplayName = $lastPostUserDisplayName;
    }

    /**
     * @return int
     */
    public function getLastPostUserId(): int
    {
        return $this->lastPostUserId;
    }

    /**
     * @param int $lastPostUserId
     */
    public function setLastPostUserId(int $lastPostUserId)
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
        $this->setPublishedOn(Carbon::instance($faker->dateTime)->toDateTimeString());
    }
}