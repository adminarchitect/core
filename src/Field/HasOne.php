<?php

namespace Terranet\Administrator\Field;

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

        $related = $relation->getRelated();
        $columns = $this->collectColumns($related)
                        ->without(array_merge([$related->getKeyName()], $this->except ?? []))
                        ->only($this->only)
                        ->each(function ($field) {
                            $field->setId(
                                "{$this->id()}.{$field->id()}"
                            );
                        });

        return [
            'columns' => $columns,
        ];
    }
}
