<?php

namespace Terranet\Administrator\Field;

use Illuminate\Support\Facades\View;
use Terranet\Administrator\Scaffolding;
use Terranet\Administrator\Traits\Module\HasColumns;

class HasOne extends BelongsTo
{
    use HasColumns;

    /** @var null|array */
    protected $only;

    /** @var null|array */
    protected $except;

    /**
     * @return array
     */
    protected function onEdit(): array 
    {
        $relation = $this->model->{$this->id()}();

        $related = $relation->getRelated();
        $columns = $this->collectColumns($related)
                        ->without($related->getKeyName())
                        ->without($this->except)
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

    /**
     * @param array $only
     * @return self
     */
    public function setOnly(array $only): self
    {
        $this->only = $only;

        return $this;
    }

    /**
     * @param array $except
     * @return self
     */
    public function setExcept(array $except): self
    {
        $this->except = $except;

        return $this;
    }
}