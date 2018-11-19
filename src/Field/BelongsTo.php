<?php

namespace Terranet\Administrator\Field;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Field\Traits\HandlesRelation;
use Terranet\Administrator\Field\Traits\WorksWithModules;

class BelongsTo extends Generic
{
    use WorksWithModules, HandlesRelation;

    /** @var string */
    protected $column = 'name';

    /** @var bool */
    protected $searchable = true;

    /**
     * @param string $column
     *
     * @return self
     */
    public function useForTitle(string $column): self
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return $this
     */
    public function searchable()
    {
        $this->searchable = true;

        return $this;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->model->{$this->id}()->getForeignKey();
    }

    /**
     * @param Builder $query
     * @param Model $model
     * @param string $direction
     *
     * @return Builder
     */
    public function sortBy(Builder $query, Model $model, string $direction): Builder
    {
        $table = $model->getTable();
        $relation = \call_user_func([$model, $this->id()]);
        $joinTable = $relation->getRelated()->getTable();
        $alias = str_random(4);

        $ownerKey = $relation->getOwnerKey();
        $foreignKey = $relation->getForeignKey();
        $foreignColumn = $this->getColumn();

        return $query->leftJoin("{$joinTable} as {$alias}", "{$table}.{$foreignKey}", '=', "{$alias}.{$ownerKey}")
                     ->orderBy("{$alias}.{$foreignColumn}", $direction);
    }

    /**
     * @return array
     */
    protected function onIndex(): array
    {
        if ($relation = $this->model->{$this->id}) {
            $title = $relation->getAttribute($this->getColumn());
            $module = $this->firstWithModel($relation);
        }

        return [
            'title' => $title ?? null,
            'relation' => $relation ?? null,
            'module' => $module ?? null,
        ];
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
    protected function onEdit(): array
    {
        if (method_exists($this->model, $this->id)) {
            $relation = $this->relation();
            $related = $this->model->{$this->id} ?: $relation->getRelated();
            $column = $this->getColumn();

            if ($this->searchable) {
                if ($value = $this->value()) {
                    $options = [
                        $value->getKey() => $value->getAttribute($column),
                    ];
                }
            } else {
                $options = $related::pluck($column, $related->getKeyName())->toArray();
            }
        }

        return [
            'options' => $options ?? [],
            'related' => $related ?? null,
            'searchable' => $this->searchable,
        ];
    }
}
