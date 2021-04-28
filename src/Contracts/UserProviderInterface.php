<?php

namespace Railroad\Railforums\Contracts;

interface UserProviderInterface
{
    /**
     * @param $userId
     * @return mixed
     */
    public function getUserAccessLevel($userId);

    /**
     * @param $userId
     * @return mixed
     */
    public function getUser($userId);

    /**
     * @param array $userIds
     * @return array
     */
    public function getUsersByIds(array $userIds):array;

    /**
     * @param array $userIds
     * @return array
     */
    public function getUsersAccessLevel(array $userIds):array;

    /**
     * @param array $userIds
     * @return array
     */
    public function getUsersXPAndRank(array $userIds):array;

}
