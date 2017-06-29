<?php

namespace Terranet\Administrator\Form\Type;

use Form;

class Number extends Text
{
    /**
     * The specific defaults for subclasses to override.
     *
     * @var array
     */
    protected $attributes = [
        'class' => 'form-control',
        'style' => 'width: 150px;',
    ];

    /**
     * The specific rules for subclasses to override.
     *
     * @var array
     */
    protected $rules = [
        'min' => 'numeric',
        'max' => 'numeric',
        'step' => 'numeric',
    ];

    public function render()
    {
        return Form::input('number', $this->getFormName(), $this->value, $this->attributes);
    }
}
