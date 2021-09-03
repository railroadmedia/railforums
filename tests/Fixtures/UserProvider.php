<?php
namespace Tests\Fixtures;

use Carbon\Carbon;
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
            $userObjects[$user->id] =  new User($user->id, $user->display_name, $user->avatar_url, Carbon::parse($user->created_at));
        }

        return $userObjects;
    }

    public function getUsersAccessLevel(array $userIds)
    : array {
        $results = [];
        foreach ($userIds as $userId){
            $results[$userId] = 'piano';
        }

        return $results;
    }

    public function getUsersXPAndRank(array $userIds)
    : array {
        $results = [];
        foreach ($userIds as $userId){
            $results[$userId] = [
                'xp' => rand(0,500),
                'xp_rank' => 'Casual',
                'level_rank' => '1.0'
            ];
        }

        return $results;
    }
}
