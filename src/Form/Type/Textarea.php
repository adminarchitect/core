<?php

namespace Terranet\Administrator\Form\Type;

use Form;
use Terranet\Administrator\Form\Element;

class Textarea extends Element
{
    /**
     * The specific defaults for subclasses to override.
     *
     * @var array
     */
    protected $attributes = [
        'style' => 'min-width: 700px; height: 150px;',
        'class' => 'form-control',
    ];

    public function render()
    {
        return Form::textarea($this->getFormName(), $this->value, $this->attributes);
    }
}
