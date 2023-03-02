<?php

namespace Railroad\Railforums\Contracts;

use Railroad\Railforums\Entities\User;

interface UserProviderInterface
{
    /**
     * @param $userId
     * @return User|null
     */
    public function getUser($userId): ?User;

    /**
     * @param array $userIds
     * @return User[]
     */
    public function getUsersByIds(array $userIds): array;

    /**
     * @param $userId
     * @return string
     */
    public function getUserAccessLevel($userId): string;

    /**
     * @param array $userIds
     * @return array
     */
    public function getUsersAccessLevel(array $userIds): array;

    /**
     * @param array $userIds
     * @return array
     */
    public function getUsersXPAndRank(array $userIds): array;

    /**
     * @param array $userIds
     * @return array
     */
    public function getAssociatedCoaches(array $userIds): array;

    /**
     * @return array|null
     */
    public function getBlockedUsers(): ?array;
}
