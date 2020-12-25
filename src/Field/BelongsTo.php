<?php

namespace Terranet\Administrator\Field;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Terranet\Administrator\Architect;
use Terranet\Administrator\Field\Traits\HandlesRelation;
use Terranet\Administrator\Field\Traits\HasEmptyValue;

class BelongsTo extends Field
{
    use HandlesRelation, HasEmptyValue;

    /** @var string */
    public $column = 'name';

    /** @var bool */
    public $searchable = true;

    /** @var string */
    public $searchUrl;

    /**
     * @param string $column
     * @return self
     */
    public function useForTitle(string $column): self
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function searchable(bool $flag = false)
    {
        $this->searchable = (bool) $flag;

        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function name(): string
    {
        return $this->getForeignKey(
            $this->model->{$this->id}()
        );
    }

    /**
     * @param Builder $query
     * @param Model $model
     * @param string $direction
     * @return Builder
     * @throws \Exception
     */
    public function sortBy(Builder $query, Model $model, string $direction): Builder
    {
        $table = $model->getTable();
        $relation = $this->relation();
        $joinTable = $relation->getRelated()->getTable();
        $alias = Str::random(4);

        $ownerKey = $relation->getOwnerKey();
        $foreignKey = $this->getForeignKey($relation);
        $foreignColumn = $this->getColumn();

        return $query->leftJoin("{$joinTable} as {$alias}", "{$table}.{$foreignKey}", '=', "{$alias}.{$ownerKey}")
            ->orderBy("{$alias}.{$foreignColumn}", $direction);
    }

    /**
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    public function dataUrl($url)
    {
        $this->searchUrl = $url;

        return $this;
    }

    /**
     * @return array
     */
    protected function onView(): array
    {
        return $this->onIndex();
    }

    /**
     * @return array
     */
    protected function onIndex(): array
    {
        if ($related = $this->model->{$this->id}) {
            $title = $related->getAttribute($this->getColumn());
            $module = Architect::resourceByEntity($related);
        }

        return [
            'title' => $title ?? null,
            'related' => $related ?? null,
            'module' => $module ?? null,
        ];
    }

    /**
     * @return array
     */
    protected function onEdit(): array
    {
        if (method_exists($this->model, $this->id)) {
            $relation = $this->relation();
            $related = $this->model->{$this->id} ?: $relation->getRelated();

            $field = $this->getColumn();

            if ($this->searchable) {
                if ($value = $this->value()) {
                    $options = [
                        $value->getKey() => $value->getAttribute($field),
                    ];
                }
            } else {
                $options = $related::pluck($field, $related->getKeyName())->toArray();
            }
        }

        if ($this->allowEmpty) {
            $options = ['' => '---'] + $options;
        }

        return [
            'options' => $options ?? [],
            'related' => $related ?? null,
            'searchIn' => $searchIn = isset($related) ? get_class($related) : null,
            'searchable' => $this->searchable,
            'searchBy' => $searchBy = $this->column,
            'searchUrl' => $this->searchUrl($searchIn, $searchBy),
        ];
    }

    protected function searchUrl(string $searchIn, string $searchBy)
    {
        return $this->searchUrl ?? strtr('/{path}/search/?searchable={searchIn}&field={searchBy}', [
                '{path}' => \Terranet\Administrator\Architect::path(),
                '{searchIn}' => $searchIn,
                '{searchBy}' => $searchBy,
            ]);
    }
}
