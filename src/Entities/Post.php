<?php

namespace Railroad\Railforums\Entities;

use Carbon\Carbon;
use Faker\Generator;
use Railroad\Railforums\DataMappers\PostDataMapper;
use Railroad\Railmap\Entity\EntityBase;
use Railroad\Railmap\Entity\Properties\SoftDelete;
use Railroad\Railmap\Entity\Properties\Timestamps;
use Railroad\Railmap\Entity\Properties\Versioned;

/**
 * Class Post
 *
 * @method Post|null getPromptingPost()
 * @method setPromptingPost(Post | null $promptingPost)
 * @method UserCloak|null getAuthor()
 * @method setAuthor(UserCloak | null $lastPost)
 * @method PostLike[] getRecentLikes()
 * @method setRecentLikes(PostLike [] $postLikes)
 * @method PostDataMapper getOwningDataMapper()
 */
class Post extends EntityBase
{
    use Timestamps, SoftDelete, Versioned;

    const STATE_PUBLISHED = 'published';
    const STATE_HIDDEN = 'hidden';

    public $versionedAttributes = [
        'content',
        'promptingPostId',
        'state',
        'edited_on',
        'published_on',
    ];

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
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $publishedOn;

    /**
     * @var string
     */
    protected $editedOn;

    /**
     * @var int
     */
    protected $likeCount = 0;

    /**
     * @var bool
     */
    protected $isLikedByCurrentUser;

    /**
     * @var int
     */
    protected $liker1Id;

    /**
     * @var string
     */
    protected $liker1DisplayName;

    /**
     * @var int
     */
    protected $liker2Id;

    /**
     * @var string
     */
    protected $liker2DisplayName;

    /**
     * @var int
     */
    protected $liker3Id;

    /**
     * @var string
     */
    protected $liker3DisplayName;

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
     * @return string
     */
    public function getPublishedOn()
    {
        return $this->publishedOn;
    }

    /**
     * @param string $publishedOn
     */
    public function setPublishedOn($publishedOn)
    {
        $this->publishedOn = $publishedOn;
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

    /**
     * @return int
     */
    public function getLikeCount(): int
    {
        return $this->likeCount;
    }

    /**
     * @param int $likeCount
     */
    public function setLikeCount(int $likeCount)
    {
        $this->likeCount = $likeCount;
    }

    /**
     * @return bool
     */
    public function getIsLikedByCurrentUser(): bool
    {
        return (boolean) $this->isLikedByCurrentUser;
    }

    /**
     * @param bool $isLikedByCurrentUser
     */
    public function setIsLikedByCurrentUser(bool $isLikedByCurrentUser)
    {
        $this->isLikedByCurrentUser = $isLikedByCurrentUser;
    }

    /**
     * @return int
     */
    public function getLiker1Id()
    {
        return $this->liker1Id;
    }

    /**
     * @param int $liker1Id
     */
    public function setLiker1Id(int $liker1Id): void
    {
        $this->liker1Id = $liker1Id;
    }

    /**
     * @return string
     */
    public function getLiker1DisplayName()
    {
        return $this->liker1DisplayName;
    }

    /**
     * @param string $liker1DisplayName
     */
    public function setLiker1DisplayName(string $liker1DisplayName): void
    {
        $this->liker1DisplayName = $liker1DisplayName;
    }

    /**
     * @return int
     */
    public function getLiker2Id()
    {
        return $this->liker2Id;
    }

    /**
     * @param int $liker2Id
     */
    public function setLiker2Id(int $liker2Id): void
    {
        $this->liker2Id = $liker2Id;
    }

    /**
     * @return string
     */
    public function getLiker2DisplayName()
    {
        return $this->liker2DisplayName;
    }

    /**
     * @param string $liker2DisplayName
     */
    public function setLiker2DisplayName(string $liker2DisplayName): void
    {
        $this->liker2DisplayName = $liker2DisplayName;
    }

    /**
     * @return int
     */
    public function getLiker3Id()
    {
        return $this->liker3Id;
    }

    /**
     * @param int $liker3Id
     */
    public function setLiker3Id(int $liker3Id): void
    {
        $this->liker3Id = $liker3Id;
    }

    /**
     * @return string
     */
    public function getLiker3DisplayName()
    {
        return $this->liker3DisplayName;
    }

    /**
     * @param string $liker3DisplayName
     */
    public function setLiker3DisplayName(string $liker3DisplayName): void
    {
        $this->liker3DisplayName = $liker3DisplayName;
    }

    public function randomize()
    {
        /** @var Generator $faker */
        $faker = app(Generator::class);
        $this->setThreadId($faker->randomNumber());
        $this->setAuthorId($faker->randomNumber());
        $this->setPromptingPostId($faker->randomNumber());
        $this->setContent($faker->paragraph());
        $this->setState(
            $faker->randomElement(
                [self::STATE_PUBLISHED, self::STATE_HIDDEN]
            )
        );
        $this->setPublishedOn(Carbon::instance($faker->dateTime())->toDateTimeString());

        if ($faker->boolean()) {
            $this->setEditedOn(Carbon::instance($faker->dateTime())->toDateTimeString());
        }
    }
}