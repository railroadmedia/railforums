<?php

namespace Railroad\Railforums\Entities;

use Faker\Generator;
use Railroad\Railforums\DataMappers\UserCloakDataMapper;
use Railroad\Railmap\Entity\EntityBase;

class UserCloak extends EntityBase
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $displayName;

    /**
     * @var string|null
     */
    protected $avatarUrl;

    /**
     * @var string
     */
    protected $permissionLevel;

    /**
     * @var string
     */
    protected $label;

    const PERMISSION_LEVEL_VIEWER = 'viewer';
    const PERMISSION_LEVEL_USER = 'user';
    const PERMISSION_LEVEL_MODERATOR = 'moderator';
    const PERMISSION_LEVEL_ADMINISTRATOR = 'administrator';

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->setOwningDataMapper(app(UserCloakDataMapper::class));
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
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
     * @param string $displayName
     */
    public function setDisplayName(string $displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @return null|string
     */
    public function getAvatarUrl()
    {
        if (empty($this->avatarUrl)) {
            return 'https://dmmior4id2ysr.cloudfront.net/assets/images/avatar.svg';
        }

        return $this->avatarUrl;
    }

    /**
     * @param null|string $avatarUrl
     */
    public function setAvatarUrl($avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;
    }

    /**
     * @return string
     */
    public function getPermissionLevel()
    {
        return $this->permissionLevel;
    }

    /**
     * @param string $permissionLevel
     */
    public function setPermissionLevel( $permissionLevel)
    {
        $this->permissionLevel = $permissionLevel;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return bool
     */
    public function canViewHiddenTopics()
    {
        return $this->permissionLevel == self::PERMISSION_LEVEL_MODERATOR ||
            $this->permissionLevel == self::PERMISSION_LEVEL_ADMINISTRATOR;
    }

    /**
     * @return bool
     */
    public function canEditAnyThreads()
    {
        return $this->permissionLevel == self::PERMISSION_LEVEL_MODERATOR ||
            $this->permissionLevel == self::PERMISSION_LEVEL_ADMINISTRATOR;
    }

    /**
     * @return bool
     */
    public function canDestroyAnyThreads()
    {
        return $this->permissionLevel == self::PERMISSION_LEVEL_MODERATOR ||
            $this->permissionLevel == self::PERMISSION_LEVEL_ADMINISTRATOR;
    }

    /**
     * @return bool
     */
    public function canPinThreads()
    {
        return $this->permissionLevel == self::PERMISSION_LEVEL_MODERATOR ||
            $this->permissionLevel == self::PERMISSION_LEVEL_ADMINISTRATOR;
    }

    /**
     * @return bool
     */
    public function canLockThreads()
    {
        return $this->permissionLevel == self::PERMISSION_LEVEL_ADMINISTRATOR;
    }

    public function canDestroyAnyPosts()
    {
        return $this->permissionLevel == self::PERMISSION_LEVEL_MODERATOR ||
            $this->permissionLevel == self::PERMISSION_LEVEL_ADMINISTRATOR;
    }

    public function canEditAnyPosts()
    {
        return $this->permissionLevel == self::PERMISSION_LEVEL_MODERATOR ||
            $this->permissionLevel == self::PERMISSION_LEVEL_ADMINISTRATOR;
    }

    /**
     * @return bool
     */
    public function canViewHiddenPosts()
    {
        return $this->canViewHiddenTopics();
    }

    public function randomize()
    {
        $faker = app(Generator::class);
    }
}