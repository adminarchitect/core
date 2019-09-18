<?php

namespace Terranet\Administrator\Field;

class Password extends Field
{
    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return parent::getAttributes() + [
                'placeholder' => trans('administrator::hints.global.optional'),
                'autocomplete' => 'off',
            ];
    }
}
