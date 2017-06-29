<?php

namespace Terranet\Administrator\Auth;

class SuperAdminRule
{
    public function validate()
    {
        if (auth('admin')->guest()) {
            return false;
        }

        if (method_exists($user = auth('admin')->user(), 'isSuperAdmin')) {
            return call_user_func([$user, 'isSuperAdmin']);
        }

        return (1 === (int) $user->getAuthIdentifier());
    }
}