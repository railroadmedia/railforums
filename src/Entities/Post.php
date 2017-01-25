<?php

namespace Railroad\Railforums\Entities;

use Carbon\Carbon;
use Faker\Generator;
use Railroad\Railforums\DataMappers\PostDataMapper;
use Railroad\Railmap\Entity\EntityBase;
use Railroad\Railmap\Entity\Properties\SoftDelete;
use Railroad\Railmap\Entity\Properties\Timestamps;
use Railroad\Railmap\Entity\Properties\Versioned;

class Post extends EntityBase
{
    use Timestamps, SoftDelete, Versioned;

    /**
     * @var int
     */
    protected $threadId;

    /**
     * @var int
     */
    protected $authorId;

    /**
     * @var int
     */
    protected $promptingPostId;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var int
     */
    protected $likes;

    /**
     * @var string
     */
    protected $postedOn;

    /**
     * @var string
     */
    protected $editedOn;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->setOwningDataMapper(app(PostDataMapper::class));
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
     * @return int
     */
    public function getPromptingPostId()
    {
        return $this->promptingPostId;
    }

    /**
     * @param int $promptingPostId
     */
    public function setPromptingPostId($promptingPostId)
    {
        $this->promptingPostId = $promptingPostId;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @param int $likes
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;
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
     * @return string
     */
    public function getEditedOn()
    {
        return $this->editedOn;
    }

    /**
     * @param string $editedOn
     */
    public function setEditedOn($editedOn)
    {
        $this->editedOn = $editedOn;
    }

    public function randomize()
    {
        /** @var Generator $faker */
        $faker = app(Generator::class);
        $this->setThreadId($faker->randomNumber());
        $this->setAuthorId($faker->randomNumber());
        $this->setPromptingPostId($faker->randomNumber());
        $this->setContent($faker->paragraph());
        $this->setLikes($faker->randomNumber());
        $this->setPostedOn(Carbon::instance($faker->dateTime())->toDateTimeString());

        if ($faker->boolean()) {
            $this->setEditedOn(Carbon::instance($faker->dateTime())->toDateTimeString());
        }
    }
}