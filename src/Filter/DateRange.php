<?php

namespace Terranet\Administrator\Filter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Contracts\Filter\Searchable;

class DateRange extends Filter implements Searchable
{
    /** @var string  */
    protected $component = 'date_range';

    /**
     * @param Builder $query
     * @param Model $model
     *
     * @return Builder
     */
    public function searchBy(Builder $query, Model $model): Builder
    {
        [$dateFrom, $dateTo] = array_values($this->value());

        if ($dateFrom && $dateTo) {
            $query->whereDate("{$model->getTable()}.{$this->id()}", '>=', $dateFrom);
            $query->whereDate("{$model->getTable()}.{$this->id()}", '<=', $dateTo);
        }

        return $query;
    }
}
