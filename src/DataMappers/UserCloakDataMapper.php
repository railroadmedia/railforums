<?php

namespace Railroad\Railforums\DataMappers;

use Illuminate\Database\Query\JoinClause;
use Railroad\Railforums\Entities\UserCloak;
use Railroad\Railmap\DataMapper\DatabaseDataMapperBase;

class UserCloakDataMapper extends DatabaseDataMapperBase
{
    protected $table = 'users';

    public function mapTo()
    {
        return [
            'id' => 'id',
            'displayName' => 'display_name',
            'avatarUrl' => 'avatar_url',
            'permissionLevel' => 'access_type',
        ];
    }

//    public function gettingQuery()
//    {
//        return parent::gettingQuery()->selectRaw(
//            'users.*, ' .
//            'user_fields.value as avatar_url'
//        )->leftJoin(
//            'user_fields',
//            function (JoinClause $query) {
//                $query->on('user_fields.user_id', '=', 'users.id')
//                    ->on(
//                        'user_fields.name',
//                        '=',
//                        'avatar-url'
//                    );
//            }
//        );
//    }

    /**
     * @return UserCloak()
     */
    public function entity()
    {
        return new UserCloak();
    }
}