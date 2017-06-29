<?php

namespace Terranet\Administrator\Contracts;

interface Sortable
{
    /**
     * Get the object order number.
     *
     * @return int
     */
    public function order();
}
