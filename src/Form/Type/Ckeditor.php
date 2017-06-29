<?php

namespace Terranet\Administrator\Form\Type;

class Ckeditor extends Textarea
{
    public function render()
    {
        $attributes = $this->attributes + ['data-editor' => 'ckeditor'];

        return \Form::textarea($this->getFormName(), $this->value, $attributes);
    }
}
