<?php

namespace Terranet\Administrator\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

class SuperAdminRule
{
    public function validate(Authenticatable $user = null)
    {
        $user = $user ?: $this->userProvider();

        if (!$user) {
            return false;
        }

        if (method_exists($user, 'isSuperAdmin')) {
            return \call_user_func([$user, 'isSuperAdmin']);
        }

        return 1 === (int) $user->getAuthIdentifier();
    }

    /**
     * @return null|Authenticatable
     */
    protected function userProvider()
    {
        return auth('admin')->user();
    }
}
