<?php

namespace Terranet\Administrator\Decorators;

use Czim\Paperclip\Contracts\AttachableInterface;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Field\Boolean;
use Terranet\Administrator\Field\Email;
use Terranet\Administrator\Field\File;
use Terranet\Administrator\Field\Generic;
use Terranet\Administrator\Field\Id;
use Terranet\Administrator\Field\Image;
use Terranet\Administrator\Field\Link;
use Terranet\Administrator\Field\Phone;
use Terranet\Administrator\Field\Rank;
use Terranet\Administrator\Field\Text;
use Terranet\Administrator\Field\Textarea;
use Terranet\Rankable\Rankable;
use Terranet\Translatable\Translatable;

class Grid
{
    /** @var Model */
    private $model;

    /**
     * Grid constructor.
     *
     * @param null|Model $model
     */
    public function __construct(Model $model = null)
    {
        $this->model = $model ?: app('scaffold.module')->model();
    }

    /**
     * @param $element
     *
     * @return mixed|\Terranet\Administrator\Field\Generic
     */
    public function make($element)
    {
        // decorate attachment
        if ($this->model instanceof AttachableInterface
            && method_exists($this->model, 'getAttachedFiles')
            && array_key_exists($element, $this->model->getAttachedFiles())
        ) {
            $file = $this->model->getAttachedFiles()[$element];
            if ($file->variants()) {
                return Image::make($element, $element);
            }

            return File::make($element, $element);
        }

        // decorate translatable attachment
        if ($this->model instanceof Translatable) {
            $translation = $this->model->getTranslationModel();

            if ($translation instanceof AttachableInterface
                && method_exists($translation, 'getAttachedFiles')
                && array_key_exists($element, $translation->getAttachedFiles())
            ) {
                return Image::make($element);
            }
        }

        // decorate table column
        if ($this->realColum($element)) {
            $field = $this->detectField($element);

            return forward_static_call_array([$field, 'make'], [$element, $element]);
        }

        return Text::make($element, $element);
    }

    /**
     * @param $column
     *
     * @return bool
     */
    protected function realColum($column)
    {
        return array_key_exists($column, $this->fetchTablesColumns());
    }

    /**
     * @param $column
     *
     * @return Generic
     */
    protected function detectField($column)
    {
        if ($column === $this->model->getKeyName()) {
            return Id::class;
        }

        $className = class_basename(
            $this->fetchTablesColumns()[$column]->getType()
        );

        switch (true) {
            case $this->model instanceof Rankable && $column === $this->model->getRankableColumn():
                return Rank::class;
            case in_array($className, ['TimeType', 'DateType', 'DateTimeType'], true):
                $type = str_replace('Type', '', $className);

                return "\\Terranet\\Administrator\\Field\\{$type}";
            case 'BooleanType' === $className:
                return Boolean::class;
            case 'TextType' === $className:
                return Textarea::class;
            case 'StringType' === $className:
                if (str_contains($column, 'email')) {
                    return Email::class;
                }

                if (str_contains($column, ['url', 'site', 'host'])) {
                    return Link::class;
                }

                if (str_contains($column, ['phone', 'gsm'])) {
                    return Phone::class;
                }
            // no break
            default:
                return Text::class;
        }
    }

    /**
     * @return array
     */
    protected function fetchTablesColumns()
    {
        return \admin\db\table_columns($this->model, true);
    }
}
