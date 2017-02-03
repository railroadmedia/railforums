<?php

namespace Railroad\Railforums\Entities;

use Carbon\Carbon;
use Faker\Generator;
use Railroad\Railforums\DataMappers\ThreadDataMapper;
use Railroad\Railmap\Entity\EntityBase;
use Railroad\Railmap\Entity\Properties\SoftDelete;
use Railroad\Railmap\Entity\Properties\Timestamps;
use Railroad\Railmap\Entity\Properties\Versioned;

/**
 * Class Thread
 *
 * @method Post|null getLastPost()
 * @method setLastPost(Post | null $lastPost)
 * @method UserCloak|null getAuthor()
 * @method setAuthor(UserCloak | null $lastPost)
 */
class Thread extends EntityBase
{
    use Timestamps, SoftDelete, Versioned;

    const STATE_PUBLISHED = 'published';
    const STATE_HIDDEN = 'hidden';

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
    protected $state;

    /**
     * @var int
     */
    protected $postCount = 0;

    /**
     * @var int|null
     */
    protected $lastPostId = 0;

    /**
     * @var string|null
     */
    protected $publishedOn;

    /**
     * @var bool
     */
    protected $isRead = false;

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
    public function getPinned(): bool
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
    public function getLocked(): bool
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
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state)
    {
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function getPostCount(): int
    {
        return $this->postCount;
    }

    /**
     * @param int $postCount
     */
    public function setPostCount(int $postCount)
    {
        $this->postCount = $postCount;
    }

    /**
     * @return int|null
     */
    public function getLastPostId()
    {
        return $this->lastPostId;
    }

    /**
     * @param int|null $lastPostId
     */
    public function setLastPostId($lastPostId)
    {
        $this->lastPostId = $lastPostId;
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
     * @return bool
     */
    public function getIsRead(): bool
    {
        return $this->isRead;
    }

    /**
     * @param bool $isRead
     */
    public function setIsRead(bool $isRead)
    {
        $this->isRead = $isRead;
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
        $this->setState(
            $faker->randomElement(
                [self::STATE_PUBLISHED, self::STATE_HIDDEN]
            )
        );
        $this->setPostCount($faker->randomNumber());
        $this->setPublishedOn(Carbon::instance($faker->dateTime)->toDateTimeString());
    }
}