<?php

namespace Terranet\Administrator\Contracts\Module;

interface Editable
{
    /**
     * Define editable fields.
     *
     * @return mixed
     */
    public function form();
}
