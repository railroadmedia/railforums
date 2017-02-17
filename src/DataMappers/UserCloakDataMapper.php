<?php

namespace Railroad\Railforums\DataMappers;

use Faker\Generator;
use Illuminate\Auth\AuthManager;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railmap\DataMapper\DatabaseDataMapperBase;

/**
 * Class UserCloakDataMapper
 *
 * @package Railroad\Railforums\DataMappers
 * @method  UserCloak|UserCloak[]|null get($idOrIds)
 */
class UserCloakDataMapper extends DatabaseDataMapperBase
{
    public $table = 'users';
    public $cacheTime = 3600;

    /**
     * @var AuthManager
     */
    protected $authManager;

    /**
     * @var UserCloak|null
     */
    private $current;

    public function __construct()
    {
        parent::__construct();

        $this->authManager = app(AuthManager::class);
    }

    public function mapTo()
    {
        return [
            'id' => 'id',
            'displayName' => 'display_name',
            'avatarUrl' => 'avatar_url',
            'permissionLevel' => 'permission_level',
            'label' => 'label',
        ];
    }

    /**
     * @return UserCloak|null
     */
    public function getCurrent()
    {
        if (!empty($this->current)) {
            return $this->current;
        }

        return $this->get($this->authManager->id());
    }

    /**
     * @return int|null
     */
    public function getCurrentId()
    {
        if (!empty($this->current)) {
            return $this->current->getId();
        }

        $current = $this->get($this->authManager->id());

        return !empty($current) ? $current->getId() : 0;
    }

    /**
     * @return string
     */
    public function getCurrentPermissionLevel()
    {
        if (!empty($this->current)) {
            return $this->current->getPermissionLevel();
        }

        $current = $this->get($this->authManager->id());

        return !empty($current) ? $current->getPermissionLevel() : UserCloak::PERMISSION_LEVEL_VIEWER;
    }

    /**
     * @return UserCloak
     */
    public function fake($permissionLevel = UserCloak::PERMISSION_LEVEL_USER)
    {
        /** @var Generator $faker */
        $faker = app(Generator::class);

        $userCloak = $this->entity();
        $userCloak->setDisplayName($faker->userName . $faker->randomNumber());
        $userCloak->setAvatarUrl('http://lorempixel.com/200/200/');
        $userCloak->setPermissionLevel(
            $permissionLevel
        );

        $userCloak->persist();

        return $userCloak;
    }

    /**
     * @param UserCloak $userCloak
     */
    public function setCurrent(UserCloak $userCloak)
    {
        $this->current = $userCloak;
    }

    /**
     * @return UserCloak()
     */
    public function entity()
    {
        return new UserCloak();
    }
}