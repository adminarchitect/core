<?php

namespace Terranet\Administrator\Contracts\Module;

use Terranet\Administrator\Collection\Mutable;

interface Filtrable
{
    /**
     * Declare scaffold filters.
     *
     * @return Mutable
     */
    public function filters();

    /**
     * Declare scaffold scopes.
     *
     * @return Mutable
     */
    public function scopes();
}
