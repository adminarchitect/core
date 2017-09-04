<?php

namespace Terranet\Administrator\Form\Collection;

use Closure;
use Terranet\Administrator\Collection\Mutable as BaseMutableCollection;
use Terranet\Administrator\Exception;
use Terranet\Administrator\Form\FormElement;
use Terranet\Administrator\Form\FormSection;
use Terranet\Administrator\Form\InputFactory;
use Terranet\Administrator\Form\Type\Ckeditor;
use Terranet\Administrator\Form\Type\Markdown;
use Terranet\Administrator\Form\Type\Medium;
use Terranet\Administrator\Form\Type\Tinymce;

class Mutable extends BaseMutableCollection
{
    /**
     * Insert a new form element.
     *
     * @param $element
     * @param mixed string|Closure $inputType
     * @param mixed null|int|string $position
     * @return $this
     * @throws Exception
     */
    public function create($element, $inputType = null, $position = null)
    {
        if (!(is_string($element) || $element instanceof FormElement)) {
            throw new Exception("\$element must be string or FormElement instance.");
        }

        # Create new element from string declaration ("title").
        if (is_string($element)) {
            $element = (new FormElement($element));
        }

        # Create Form Input Element from string declaration ("textarea")
        if (is_string($inputType)) {
            $oldInput = $element->getInput();
            $newInput = InputFactory::make($element->id(), $inputType);

            $newInput->setRelation(
                $oldInput->getRelation()
            )->setTranslatable(
                $oldInput->getTranslatable()
            );

            $element->setInput(
                $newInput
            );
        }

        # Allow a callable input type.
        if (is_callable($inputType)) {
            call_user_func_array($inputType, [$element]);
        }

        if (is_numeric($position)) {
            return $this->insert($element, $position);
        }

        # Push element
        $this->push($element);

        if (null !== $position) {
            return $this->move($element->id(), $position);
        }

        return $this;
    }

    /**
     * Create a section.
     *
     * @param $section
     * @param null $position
     * @return $this
     */
    public function section($section, $position = null)
    {
        if (is_string($section)) {
            $section = new FormSection($section);
        }

        return null !== $position ? $this->insert($section, $position) : $this->push($section);
    }

    public function hasEditors($editor)
    {
        $this->validateEditor($editor);

        return !!$this->filter(function (FormElement $element) use ($editor) {
            $input = $element->getInput();

            if ('ckeditor' === $editor) {
                return $input instanceof Ckeditor;
            }

            if ('medium' === $editor) {
                return $input instanceof Medium;
            }

            if ('markdown' === $editor) {
                return $input instanceof Markdown;
            }

            return $input instanceof Tinymce;
        })->count();
    }

    /**
     * @param $editor
     * @throws Exception
     */
    protected function validateEditor($editor)
    {
        if (!in_array($editor, ['ckeditor', 'tinymce', 'medium', 'markdown'])) {
            throw new Exception(sprintf("Unknown editor %s", $editor));
        }
    }

    /**
     * Create element object from string.
     *
     * @param $element
     * @return mixed
     */
    protected function createElement($element)
    {
        if (is_string($element)) {
            $element = new FormElement($element);
        }

        return $element;
    }
}