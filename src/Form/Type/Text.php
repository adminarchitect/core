<?php

namespace Terranet\Administrator\Form\Type;

use Form;
use Terranet\Administrator\Form\Element;

class Text extends Element
{
    /**
     * The specific defaults for subclasses to override.
     *
     * @var array
     */
    protected $attributes = [
        'style' => 'width: 100%;',
        'class' => 'form-control',
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    protected $rules = [
        'maxlength' => 'integer|min:0|max:255',
    ];

    public function render()
    {
        return Form::text($this->getFormName(), $this->value, $this->attributes);
    }
}
