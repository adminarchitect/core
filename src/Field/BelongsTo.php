<?php

namespace Terranet\Administrator\Field;

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
    public function name()
    {
        return $this->model->{$this->id}()->getForeignKey();
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
