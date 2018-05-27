<?php

namespace Terranet\Administrator\Decorators;

use Czim\Paperclip\Contracts\AttachableInterface;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Columns\Decorators\AttachmentDecorator;
use Terranet\Administrator\Columns\Decorators\BooleanDecorator;
use Terranet\Administrator\Columns\Decorators\CellDecorator;
use Terranet\Administrator\Columns\Decorators\DatetimeDecorator;
use Terranet\Administrator\Columns\Decorators\RankDecorator;
use Terranet\Administrator\Columns\Decorators\StringDecorator;
use Terranet\Administrator\Columns\Decorators\TextDecorator;
use Terranet\Administrator\Columns\Element;
use Terranet\Rankable\Rankable;
use Terranet\Translatable\Translatable;

class Grid
{
    /**
     * @var Model
     */
    private $model;

    public function __construct(Model $model = null)
    {
        $this->model = $model ?: app('scaffold.module')->model();
    }

    public function makeElement($element)
    {
        if (is_string($element)) {
            $element = new Element($element);
            $element->display(
                $this->getDecorator($element->id())
            );
        }

        return $element;
    }

    /**
     * @param $column
     *
     * @return CellDecorator
     */
    protected function getDecorator($column)
    {
        // decorate attachment
        if ($this->model instanceof AttachableInterface
            && method_exists($this->model, 'getAttachedFiles')
            && array_key_exists($column, $this->model->getAttachedFiles())
        ) {
            return new AttachmentDecorator($column);
        }

        // decorate translatable attachment
        if ($this->model instanceof Translatable) {
            $translation = $this->model->getTranslationModel();

            if ($translation instanceof AttachableInterface
                && method_exists($translation, 'getAttachedFiles')
                && array_key_exists($column, $translation->getAttachedFiles())
            ) {
                return new AttachmentDecorator($column);
            }
        }

        // decorate table column
        if ($this->realColum($column)) {
            return $this->decorateByType($column);
        }

        return new StringDecorator($column);
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
     * @return CellDecorator
     */
    protected function decorateByType($column)
    {
        $className = class_basename(
            $this->fetchTablesColumns()[$column]->getType()
        );

        if ($this->model instanceof Rankable && $column === $this->model->getRankableColumn()) {
            return new RankDecorator($column);
        }

        if (in_array($className, ['TimeType', 'DateType', 'DateTimeType'], true)) {
            return (new DatetimeDecorator($column))->setType($className);
        }

        if (in_array($className, ['BooleanType'], true)) {
            return new BooleanDecorator($column);
        }

        if (in_array($className, ['TextType'], true)) {
            return new TextDecorator($column);
        }

        return new StringDecorator($column);
    }

    /**
     * @return array
     */
    protected function fetchTablesColumns()
    {
        return \admin\db\table_columns(
            $this->model,
            true
        );
    }
}
