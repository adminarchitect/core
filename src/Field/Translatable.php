<?php

namespace Terranet\Administrator\Field;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Terranet\Administrator\Scaffolding;
use Terranet\Localizer\Locale;

/**
 * Class Translatable.
 *
 * @method switchTo(string $className)
 * @method tinymce()
 * @method ckeditor()
 * @method markdown()
 * @method medium()
 * @method hideLabel()
 * @method sortable(\Closure $callback = null)
 * @method disableSorting()
 */
class Translatable
{
    /** @var Field */
    protected $field;

    /**
     * Translatable constructor.
     *
     * @param Field $field
     */
    protected function __construct(Field $field)
    {
        $this->field = $field;
    }

    /**
     * Proxy field methods calls.
     *
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (\in_array($method, Textarea::KNOWN_EDITORS, true)) {
            return new static($this->field->$method());
        }

        if (method_exists($this->field, $method)) {
            return \call_user_func_array([$this->field, $method], $args);
        }
    }

    /**
     * @param Field $field
     *
     * @return Translatable
     */
    public static function make(Field $field)
    {
        return new static($field);
    }

    /**
     * @param string $page
     *
     * @return mixed
     */
    public function render(string $page = 'index')
    {
        if (\in_array($page, [Scaffolding::PAGE_INDEX, Scaffolding::PAGE_VIEW], true)) {
            if ($this->field->hasCustomFormat()) {
                return $this->field->callFormatter($this->field->getModel(), $page);
            }

            if ($presenter = $this->field->hasPresenter($this->field->getModel(), $this->field->id())) {
                return $this->field->callPresenter($presenter);
            }
        }

        $data = [
            'field' => $this->field,
            'model' => $this->field->getModel(),
        ];

        if (method_exists($this, $dataGetter = 'on'.Str::title($page))) {
            $data += \call_user_func([$this, $dataGetter]);
        }

        return View::make('administrator::fields.translatable.'.$page, $data);
    }

    /**
     * @param Locale $language
     *
     * @return string
     */
    public function name(Locale $language)
    {
        return "translatable[{$language->id()}][{$this->field->id()}]";
    }

    /**
     * @param Locale $language
     *
     * @return null|mixed
     */
    public function value(Locale $language)
    {
        $model = $this->field->getModel();
        $entity = $model->translate($language->id());

        return $entity ? $entity->getAttribute($this->field->id()) : null;
    }

    /**
     * @return array
     */
    protected function onEdit(): array
    {
        return [
            'languages' => \localizer\locales(),
            'locale' => \localizer\locale(),
            'container' => $this,
            'translations' => app('scaffold.translations'),
        ];
    }
}
