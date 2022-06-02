<?php

namespace Railroad\Railforums\Entities;

use Carbon\Carbon;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer")
     * @var int
     */
    private int $id;
    private string $displayName = '';
    private string $profilePictureUrl = '';
    private Carbon $createdAt;
    private string $timezone = '';

    /**
     * @param int $id
     * @param string $displayName
     * @param string $profilePictureUrl
     * @param Carbon $createdAt
     * @param string $timezone
     */
    public function __construct(
        int $id,
        string $displayName,
        string $profilePictureUrl,
        Carbon $createdAt,
        string $timezone = 'Europe/Bucharest'
    ) {
        $this->id = $id;
        $this->displayName = $displayName;
        $this->profilePictureUrl = $profilePictureUrl;
        $this->createdAt = $createdAt;
        $this->timezone = $timezone;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param $displayName
     */
    public function setDisplayName($displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * @return string
     */
    public function getProfilePictureUrl(): string
    {
        return $this->profilePictureUrl;
    }

    /**
     * @param $profilePictureUrl
     */
    public function setProfilePictureUrl($profilePictureUrl): void
    {
        $this->profilePictureUrl = $profilePictureUrl;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    /**
     * @param Carbon $createdAt
     */
    public function setCreatedAt(Carbon $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        /*
        method needed by UnitOfWork
        https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/cookbook/custom-mapping-types.html
        */
        return (string)$this->getId();
    }
}