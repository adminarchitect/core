<?php

namespace Terranet\Administrator\Field;

class Number extends Field
{
    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return parent::getAttributes() + [
                'min' => 0,
                'max' => null,
                'step' => 1,
                'style' => 'width: 150px',
            ];
    }
}
