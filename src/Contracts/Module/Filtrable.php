<?php

namespace Terranet\Administrator\Contracts\Module;

interface Filtrable
{
    /**
     * Declare scaffold filters.
     *
     * @example: return filter_text("Name");
     *
     * @return array
     */
    public function filters();

    /**
     * Declare scaffold filters.
     *
     * @example: return filter_text("Name");
     *
     * @return array
     */
    public function scopes();
}
