<?php
namespace Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Railroad\Railforums\Contracts\UserProviderInterface;
use Railroad\Railforums\Entities\User;


class UserProvider implements UserProviderInterface
{
    public function getCurrentUser()
    {
        if (!auth()->id()) {
            return null;
        }

        $user =
            DB::table('users')
                ->find(auth()->id());

        if ($user) {
            return new User($user->id, $user->display_name, $user->avatar_url);
        }

        return null;
    }

    public function getUserAccessLevel($userId)
    {
        // TODO: Implement getUserAccessLevel() method.
    }

    public function getUser($userId)
    {
        $user =
            DB::table('users')
                ->find($userId);

        if ($user) {
            return new User($user->id, $user->display_name, $user->avatar_url, $user->created_at);
        }

        return null;
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getUsersByIds(array $ids): array
    {
        $users =
            DB::table('users')
                ->whereIn('id', $ids)
                ->get();

        $userObjects = [];

        foreach ($users as $user) {
            $userObjects[$user->id] =  new User($user->id, $user->display_name, $user->avatar_url, $user->created_at);
        }

        return $userObjects;
    }
}
