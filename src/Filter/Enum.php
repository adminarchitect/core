<?php

namespace Terranet\Administrator\Filter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Contracts\Filter\Searchable;

class Enum extends Filter implements Searchable
{
    /** @var array */
    protected $options;

    /**
     * @param array $options
     *
     * @return self
     */
    public function setOptions(array $options = []): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param Builder $query
     * @param Model $model
     *
     * @return Builder
     */
    public function searchBy(Builder $query, Model $model): Builder
    {
        if (!\is_array($value = $this->value())) {
            $value = [$value];
        }

        return $query->whereIn("{$model->getTable()}.{$this->id()}", $value);
    }

    /**
     * @return array
     */
    protected function renderWith()
    {
        return [
            'options' => $this->options,
        ];
    }
}
