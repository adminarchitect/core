<?php

namespace Terranet\Administrator\Traits\Policy;

use Illuminate\Foundation\Auth\User;

trait GrantsSuperPrivileges
{
    public function before(User $auth, $ability, $editable = null)
    {
        if (method_exists($this, $ability)) {
            return \call_user_func_array([$this, $ability], [$auth, $editable]);
        }

        return $auth->isSuperAdmin();
    }
}
