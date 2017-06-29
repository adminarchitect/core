<?php

namespace Terranet\Administrator\Contracts\Services;

interface Widgetable
{
    /**
     * Widget contents.
     *
     * @return mixed
     */
    public function render();
}
