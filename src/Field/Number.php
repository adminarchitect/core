<?php

namespace Terranet\Administrator\Field;

class Number extends Generic
{
    /** @var array */
    protected $attributes = [
        'min' => null,
        'max' => null,
        'step' => null,
    ];

    /**
     * @return array
     */
    protected function onEdit(): array
    {
        return [
            'attributes' => $this->attributes,
        ];
    }
}
