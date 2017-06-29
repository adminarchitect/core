<?php

namespace Terranet\Administrator\Form\Type;

use Form;

class Tinymce extends Textarea
{
    public function render()
    {
        $attributes = $this->attributes + ['data-editor' => 'tinymce'];

        return Form::textarea($this->getFormName(), $this->value, $attributes);
    }
}
