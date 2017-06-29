<?php

namespace Terranet\Administrator\Traits\Module;

use Terranet\Administrator\Form\Collection\Mutable;
use Terranet\Administrator\Form\FormElement;

trait HasOptions
{
    /**
     * List of editable options
     *
     * @return Mutable
     */
    public function form()
    {
        return $this->scaffoldForm();
    }

    /**
     * @return Mutable
     */
    protected function scaffoldForm()
    {
        $collection = new Mutable;

        foreach (options_fetch() as $option) {
            $element = FormElement::text($option->key);
            $element->getInput()->setValue($option->value);

            $collection->push(
                $element
            );
        }

        return $collection;
    }
}
