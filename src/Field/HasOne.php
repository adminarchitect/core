<?php

namespace Terranet\Administrator\Field;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Collection\Mutable;
use Terranet\Administrator\Traits\Module\HasColumns;

class HasOne extends BelongsTo
{
    use HasColumns;

    /** @var null|array */
    protected $only;

    /** @var null|array */
    protected $except;

    /** @var null|Closure */
    protected $withColumnsCallback;

    /**
     * Fetch related columns.
     *
     * @return null|Mutable
     */
    protected function getColumns(): ?Mutable
    {
        $relation = $this->model->{$this->id()}();

        return $this->applyColumnsCallback(
            $this->relatedColumns($related = $relation->getRelated())
                ->each->setModel($this->model->{$this->id()} ?: $related)
        );
    }

    /**
     * @param  array  $only
     * @return self
     */
    public function only(array $only): self
    {
        $this->only = $only;

        return $this;
    }

    /**
     * @param  array  $except
     * @return self
     */
    public function except(array $except): self
    {
        $this->except = $except;

        return $this;
    }

    /**
     * @return array
     */
    protected function onEdit(): array
    {
        $columns = $this->getColumns()->each(function ($field) {
            $field->setId(
                "{$this->id()}.{$field->id()}"
            );
        });

        return [
            'columns' => $columns,
        ];
    }

    /**
     * @return array
     */
    protected function onIndex(): array
    {
        $columns = $this->getColumns()->filter(function ($field) {
            return !$field instanceof Textarea;
        });

        if ($model = $this->model->{$this->id()}) {
            $columns->each->setModel($model);
        };

        return [
            'columns' => $columns,
        ];
    }

    /**
     * @return array
     */
    protected function onView(): array
    {
        return [
            'columns' => $this->getColumns(),
            'related' => $this->model->{$this->id()},
        ];
    }

    /**
     * @param $related
     * @return Mutable
     */
    protected function relatedColumns($related): Mutable
    {
        return $this->collectColumns($related)
            ->except(array_merge([$related->getKeyName()], $this->except ?? []))
            ->only($this->only);
    }

    /**
     * @param  Closure  $callback
     * @return $this
     */
    public function withColumns(Closure $callback): self
    {
        $this->withColumnsCallback = $callback;

        return $this;
    }

    /**
     * Apply callback function to all columns, including those added during callback execution.
     *
     * @param  Mutable  $collection
     * @return mixed|Mutable
     */
    protected function applyColumnsCallback(Mutable $collection)
    {
        if ($this->withColumnsCallback) {
            $collection = call_user_func_array($this->withColumnsCallback, [$collection, $this->model]);
        }

        $this->assignModel(
            $collection,
            $this->model->{$this->id()} ?: $this->model->{$this->id()}()->getRelated()
        );

        return $collection;
    }

    /**
     * @param  Mutable  $collection
     * @param $model
     * @return mixed
     */
    protected function assignModel(Mutable $collection, $model)
    {
        return $collection->each->setModel($model);
    }
}
