<?php

namespace Railroad\Railforums\Entities;

use Carbon\Carbon;
use DateTimeZone;
use Exception;

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
    private $xp = 0;
    private string $xpRank = '';
    private string $levelRank = '1.1';
    private string $accessLevel = '';

    /**
     * @param int $id
     * @param string $displayName
     * @param string $profilePictureUrl
     * @param Carbon $createdAt
     * @param string $timezone
     * @param int $xp
     * @param string $xpRank
     * @param string $levelRank
     * @param string $accessLevel
     */
    public function __construct(
        int $id,
        string $displayName,
        string $profilePictureUrl,
        Carbon $createdAt,
        string $timezone = 'Europe/Bucharest',
        $xp = null,
        $xpRank = '',
        $levelRank = '1.1',
        $accessLevel = 'pack'
    ) {
        $this->id = $id;
        $this->displayName = $displayName;
        $this->profilePictureUrl = $profilePictureUrl;
        $this->createdAt = $createdAt;
        $this->timezone = $timezone;
        $this->xp = $xp;
        $this->xpRank = $xpRank;
        $this->levelRank = $levelRank;
        $this->accessLevel = $accessLevel;
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
        return $this->isValidTimezoneId($this->timezone) ? $this->timezone : 'America/Los_Angeles';
    }

    /**
     * @param $timezoneId
     * @return bool
     */
    public function isValidTimezoneId($timezoneId)
    {
        if (empty($timezoneId)) {
            return false;
        }

        try {
            new DateTimeZone($timezoneId);
        } catch (Exception $e) {
            return false;
        }
        return true;
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

    /**
     * @return int
     */
    public function getXp(): int
    {
        return $this->xp;
    }

    /**
     * @param mixed $xp
     */
    public function setXp($xp): void
    {
        $this->xp = $xp;
    }

    /**
     * @return string
     */
    public function getXpRank(): string
    {
        return $this->xpRank;
    }

    /**
     * @param $profilePictureUrl
     */
    public function setXpRank($xpRank): void
    {
        $this->xpRank = $xpRank;
    }
    /**
     * @return string
     */
    public function getLevelRank(): string
    {
        return $this->levelRank;
    }

    /**
     * @param $levelRank
     */
    public function setLevelRank($levelRank): void
    {
        $this->levelRank = $levelRank;
    }
    /**
     * @return string
     */
    public function getAccessLevel(): string
    {
        return $this->accessLevel;
    }

    /**
     * @param $levelRank
     */
    public function setAccessLevel($accessLevel): void
    {
        $this->accessLevel = $accessLevel;
    }
}