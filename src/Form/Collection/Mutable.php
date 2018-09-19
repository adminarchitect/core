<?php

namespace Terranet\Administrator\Form\Collection;

use Terranet\Administrator\Collection\Mutable as BaseMutableCollection;
use Terranet\Administrator\Columns\MediaElement;
use Terranet\Administrator\Exception;
use Terranet\Administrator\Field\Textarea;
use Terranet\Administrator\Form\FormElement;
use Terranet\Administrator\Form\FormSection;
use Terranet\Administrator\Form\InputFactory;

class Mutable extends BaseMutableCollection
{
    /**
     * Whether the collection has active editor of specific type.
     *
     * @param $editor
     *
     * @throws Exception
     *
     * @return bool
     */
    public function hasEditors($editor)
    {
        $this->validateEditor($editor);

        return (bool) $this->filter(function ($field) use ($editor) {
            return $field instanceof Textarea && $field->editorEnabled($editor);
        })->count();
    }

    /**
     * Set rich editors.
     *
     * @param mixed string|array $fields
     */
    public function editors($fields, string $editor = null)
    {
        if (is_array($fields)) {
            foreach ($fields as $field => $editor) {
                $this->editors($field, $editor);
            }
        } elseif (is_string($fields) && $editor) {
            $item = $this->find($fields);
            if ($item instanceof Textarea) {
                if (method_exists($item, $editor)) {
                    $item->$editor();
                }
            }
        }

        return $this;
    }

    /**
     * Set fields descriptions.
     *
     * @param mixed string|array $fields
     */
    public function hints($fields, string $hint = null)
    {
        if (is_array($fields)) {
            foreach ($fields as $field => $hint) {
                $this->hints($field, $hint);
            }
        } elseif (is_string($fields) && $hint) {
            $item = $this->find($fields);
            $item->setDescription($hint);
        }

        return $this;
    }

    /**
     * @param $editor
     *
     * @throws Exception
     */
    protected function validateEditor($editor)
    {
        if (!in_array($editor, ['ckeditor', 'tinymce', 'medium', 'markdown'], true)) {
            throw new Exception(sprintf('Unknown editor %s', $editor));
        }
    }
}
