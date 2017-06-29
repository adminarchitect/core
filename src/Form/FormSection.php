<?php

namespace Terranet\Administrator\Form;

class FormSection extends FormElement
{
    public function __construct($id, $title = null)
    {
        parent::__construct($id);

        if ($title) {
            $this->setTitle($title);
        }
    }
}