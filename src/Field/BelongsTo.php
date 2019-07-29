<?php

namespace Terranet\Administrator\Field;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Architect;
use Terranet\Administrator\Field\Traits\HandlesRelation;

class BelongsTo extends Field
{
    use HandlesRelation;

    /** @var string */
    protected $column = 'name';

    /** @var bool */
    protected $searchable = true;

    /**
     * @param  string  $column
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
     * @param  bool  $flag
     * @return $this
     */
    public function searchable(bool $flag = false)
    {
        $this->searchable = (bool) $flag;

        return $this;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->getForeignKey(
            $this->model->{$this->id}()
        );
    }

    /**
     * @param  Builder  $query
     * @param  Model  $model
     * @param  string  $direction
     * @return Builder
     */
    public function sortBy(Builder $query, Model $model, string $direction): Builder
    {
        $table = $model->getTable();
        $relation = $this->relation();
        $joinTable = $relation->getRelated()->getTable();
        $alias = str_random(4);

        $ownerKey = $relation->getOwnerKey();
        $foreignKey = $this->getForeignKey($relation);
        $foreignColumn = $this->getColumn();

        return $query->leftJoin("{$joinTable} as {$alias}", "{$table}.{$foreignKey}", '=', "{$alias}.{$ownerKey}")
            ->orderBy("{$alias}.{$foreignColumn}", $direction);
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
            'searchIn' => isset($related) ? get_class($related) : null,
            'searchable' => $this->searchable,
            'searchBy' => $this->column,
        ];
    }
}
