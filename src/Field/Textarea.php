<?php

namespace Terranet\Administrator\Field;

use Terranet\Administrator\Scaffolding;

class Textarea extends Field
{
    const EDITOR_TINYMCE = 'tinymce';
    const EDITOR_CKEDITOR = 'ckeditor';
    const EDITOR_MEDIUM = 'medium';
    const EDITOR_MARKDOWN = 'markdown';

    const KNOWN_EDITORS = [
        self::EDITOR_MEDIUM,
        self::EDITOR_CKEDITOR,
        self::EDITOR_MARKDOWN,
        self::EDITOR_TINYMCE,
    ];

    /** @var array */
    public $visibility = [
        Scaffolding::PAGE_INDEX => false,
        Scaffolding::PAGE_EDIT => true,
        Scaffolding::PAGE_VIEW => true,
    ];

    public $editor = false;

    /**
     * @param $editor
     *
     * @return bool
     */
    public function editorEnabled($editor)
    {
        return $editor === $this->editor;
    }

    /**
     * @return $this
     */
    public function tinymce()
    {
        $this->editor = static::EDITOR_TINYMCE;

        return $this;
    }

    /**
     * @return $this
     */
    public function ckeditor()
    {
        $this->editor = static::EDITOR_CKEDITOR;

        return $this;
    }

    /**
     * @return $this
     */
    public function medium()
    {
        $this->editor = static::EDITOR_MEDIUM;

        return $this;
    }

    /**
     * @return $this
     */
    public function markdown()
    {
        $this->editor = static::EDITOR_MARKDOWN;

        return $this;
    }

    /**
     * @return array
     */
    protected function onEdit(): array
    {
        return $this->editor ? [
            'dataEditor' => $this->editor,
        ] : [];
    }
}
