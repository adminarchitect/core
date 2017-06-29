<?php

namespace Terranet\Administrator\Contracts\Services;

interface Saver
{
    /**
     * Process request and persist data.
     *
     * @return mixed
     */
    public function sync();
}
