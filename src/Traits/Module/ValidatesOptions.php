<?php

namespace Terranet\Administrator\Traits\Module;

trait ValidatesOptions
{
    /**
     * Define validation rules.
     */
    public function rules()
    {
        return $this->scaffoldRules();
    }

    /**
     * Build a list of supposed validators based on columns and indexes information.
     *
     * @return array
     */
    protected function scaffoldRules()
    {
        return array_build(options_fetch(), function ($key, $option) {
            return [$option->key, 'required'];
        });
    }
}
