<?php

namespace Terranet\Administrator\Contracts\Module;

interface Sortable
{
    /**
     * Define list of sortable columns.
     *
     * @return mixed
     */
    public function sortable();
}
