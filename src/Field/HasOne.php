<?php

namespace Terranet\Administrator\Field;

use Terranet\Administrator\Collection\Mutable;
use Terranet\Administrator\Traits\Module\HasColumns;

class HasOne extends BelongsTo
{
    use HasColumns;

    /** @var null|array */
    protected $only;

    /** @var null|array */
    protected $except;

    /**
     * @param array $only
     *
     * @return self
     */
    public function only(array $only): self
    {
        $this->only = $only;

        return $this;
    }

    /**
     * @param array $except
     *
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
        $relation = $this->model->{$this->id()}();

        $columns = $this->relatedColumns($relation->getRelated())
                        ->each(function ($field) {
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
        $relation = $this->model->{$this->id()}();

        $columns = $this->relatedColumns($relation->getRelated())
                        ->filter(function ($field) {
                            return !$field instanceof Textarea;
                        });

        return [
            'columns' => $columns,
            'related' => $this->model->{$this->id()},
        ];
    }

    /**
     * @param $related
     * @return \Terranet\Administrator\Collection\Mutable
     */
    protected function relatedColumns($related): Mutable
    {
        return $this->collectColumns($related)
                    ->except(array_merge([$related->getKeyName()], $this->except ?? []))
                    ->only($this->only);
    }
}
