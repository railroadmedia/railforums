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
    public function getPermissionLevel(): string
    {
        return $this->permissionLevel;
    }

    /**
     * @param string $permissionLevel
     */
    public function setPermissionLevel(string $permissionLevel)
    {
        $this->permissionLevel = $permissionLevel;
    }

    public function randomize()
    {
        $faker = app(Generator::class);
    }
}