<?php

namespace Terranet\Administrator\Filter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Contracts\Filter\Searchable;

class Number extends Filter implements Searchable
{
    /** @var string  */
    protected $component = 'number';

    /**
     * @param Builder $query
     * @param Model $model
     *
     * @return Builder
     */
    public function searchBy(Builder $query, Model $model): Builder
    {
        return $query->where("{$table->getTable()}.{$this->id()}", '=', (int) $this->value());
    }
}
