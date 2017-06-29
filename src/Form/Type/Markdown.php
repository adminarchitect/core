<?php

namespace Terranet\Administrator\Form\Type;

use Form;

class Markdown extends Textarea
{
    public function render()
    {
        $attributes = $this->attributes + ['data-editor' => 'markdown'];

        return Form::textarea($this->getFormName(), $this->value, $attributes);
    }
}
