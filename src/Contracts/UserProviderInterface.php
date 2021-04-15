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
     * @return mixed
     */
    public function getUsersByIds(array $userIds);

}
