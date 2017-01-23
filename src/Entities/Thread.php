<?php

namespace Railroad\Railforums\Entities;

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
    public function setPostedOn(string $postedOn)
    {
        $this->postedOn = $postedOn;
    }

    public function randomize()
    {
        $faker = app(Generator::class);
    }
}