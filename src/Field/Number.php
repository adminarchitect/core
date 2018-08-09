<?php

namespace Terranet\Administrator\Field;

use Illuminate\Support\Facades\View;
use Terranet\Administrator\Exception;
use Terranet\Administrator\Scaffolding;

class Number extends Generic
{
    /** @var array */
    protected $attributes = [
        'min' => null,
        'max' => null,
        'step' => null,
    ];

    /**
     * @return mixed
     */
    protected function onEdit()
    {
        return [
            'attributes' => $this->attributes,
        ];
    }
}
