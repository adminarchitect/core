<?php

namespace Terranet\Administrator\Form\Type;

class Tel extends Text
{
    /**
     * The specific defaults for subclasses to override.
     *
     * @var array
     */
    protected $attributes = [
        'style' => 'width: 150px;',
        'class' => 'form-control',
    ];

    /**
     * The specific rules for subclasses to override.
     *
     * @var array
     */
    protected $rules = [];

    public function render()
    {
        return \Form::input('tel', $this->getFormName(), $this->value, $this->attributes);
    }
}
