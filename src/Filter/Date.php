<?php

namespace Terranet\Administrator\Filter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Contracts\Filter\Searchable;

class Date extends Filter implements Searchable
{
    /** @var string  */
    protected $component = 'date';

    /**
     * @param Builder $query
     * @param Model $model
     *
     * @return Builder
     */
    public function searchBy(Builder $query, Model $model): Builder
    {
        return $query->whereDate("{$model->getTable()}.{$this->id()}", '=', $this->value());
    }
}
