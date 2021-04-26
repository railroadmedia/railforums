<?php

namespace Tests\Resources\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * User
 *
 * @property integer $id
 * @property string $email
 */
class User extends Authenticatable
{
    protected $table = 'users';
}
