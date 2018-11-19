<?php

namespace Terranet\Administrator;

use Terranet\Administrator\Traits\CallableTrait;

class PermissionChecker implements Contracts\Guard
{
    use CallableTrait;

    /**
     * Check permissions.
     *
     * @param $permission
     *
     * @return bool
     */
    public function isPermissionGranted($permission)
    {
        if (\is_callable($permission)) {
            return $this->callback($permission);
        }

        return (bool) $permission;
    }
}
