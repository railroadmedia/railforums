<?php

namespace Railroad\Railforums\DataMappers;

use Faker\Generator;
use Illuminate\Auth\AuthManager;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railmap\DataMapper\DatabaseDataMapperBase;

class UserCloakDataMapper extends DatabaseDataMapperBase
{
    public $table = 'users';

    /**
     * @var AuthManager
     */
    protected $authManager;

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
            'permissionLevel' => 'access_type',
        ];
    }

    /**
     * @return UserCloak
     */
    public function getCurrent()
    {
        if (!empty($this->current)) {
            return $this->current;
        }

        return $this->get($this->authManager->id());
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