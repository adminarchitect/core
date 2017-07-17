<?php

namespace App\Http\Terranet\Administrator\Modules;

use App\User;
use Terranet\Administrator\Modules\Users as CoreUsersModule;

/**
 * Administrator Users Module
 *
 * @package Terranet\Administrator
 */
class Users extends CoreUsersModule
{
    //
    protected $model = User::class;
}
