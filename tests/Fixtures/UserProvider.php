<?php
namespace Tests\Fixtures;

use Illuminate\Support\Facades\DB;
use Railroad\Railforums\Contracts\UserProviderInterface;
use Tests\Resources\Models\User;

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
            return new User($user->id);
        }

        return null;
    }
}
