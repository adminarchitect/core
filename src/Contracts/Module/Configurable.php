<?php

namespace Terranet\Administrator\Contracts\Module;

interface Configurable
{
    /**
     * Get array of settings with form types.
     *
     * @return mixed
     */
    public function settings();
}
