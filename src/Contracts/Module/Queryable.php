<?php

namespace Terranet\Administrator\Contracts\Module;

interface Queryable
{
    /**
     * Extend scaffold query.
     *
     * @param $query
     *
     * @return mixed
     */
    public function query($query);
}
