<?php

namespace Terranet\Administrator\Contracts\Form;

interface Validable
{
    /**
     * Set validation rules.
     *
     * @param array $rules
     *
     * @return mixed
     */
    public function setRules(array $rules = []);
}
