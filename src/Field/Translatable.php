<?php

namespace Terranet\Administrator\Field;

use Illuminate\Support\Facades\View;
use League\Flysystem\Adapter\Local;
use function localizer\locale;
use Terranet\Administrator\Exception;
use Terranet\Localizer\Locale;

/**
 * Class Translatable
 *
 * @package Terranet\Administrator\Field
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
    /** @var Generic */
    protected $field;

    /**
     * Translatable constructor.
     *
     * @param Generic $field
     */
    protected function __construct(Generic $field)
    {
        $this->field = $field;
    }

    /**
     * @param Generic $field
     * @return Translatable
     */
    public static function make(Generic $field)
    {
        return new static($field);
    }

    /**
     * Proxy field methods calls.
     *
     * @param $method
     * @param $args
     */
    public function __call($method, $args)
    {
        if (method_exists($this->field, $method)) {
            return call_user_func_array([$this->field, $method], $args);
        }
    }

    /**
     * @param string $page
     * @return mixed
     */
    public function render(string $page = 'index')
    {
        if ($this->field->hasFormat()) {
            // Each Field can define its own data for custom formatter.
            $withData = method_exists($this, 'renderWith')
                ? $this->renderWith()
                : [$this->field->value(), $this->field->getModel()];

            return $this->field->callFormatter($withData);
        }

        $data = [
            'field' => $this->field,
            'model' => $this->field->getModel(),
        ];

        if (method_exists($this, $dataGetter = 'on'.title_case($page))) {
            $data += call_user_func([$this, $dataGetter]);
        }

        return View::make('administrator::fields.translatable.'.$page, $data);
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

    /**
     * @param Language $language
     */
    public function name(Locale $language)
    {
        return "translatable[{$language->id()}][{$this->field->id()}]";
    }

    /**
     * @param Locale $language
     */
    public function value(Locale $language)
    {
        $model = $this->field->getModel();
        $entity = $model->translate($language->id());

        return $entity ? $entity->getAttribute($this->field->id()) : null;
    }
}