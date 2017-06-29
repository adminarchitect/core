<?php

namespace Terranet\Administrator\Contracts;

interface Guard
{
    /**
     * Check permissions.
     *
     * @param $permission
     *
     * @return bool
     */
    public function isPermissionGranted($permission);
}
