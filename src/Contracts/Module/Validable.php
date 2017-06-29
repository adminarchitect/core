<?php

namespace Terranet\Administrator\Contracts\Module;

interface Validable
{
    /**
     * Validation rules.
     *
     * @return mixed
     */
    public function rules();
}
