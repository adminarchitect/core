<?php

namespace Terranet\Administrator\Contracts\Filter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

interface Searchable
{
    /**
     * @param Builder $query
     * @param Model $model
     * @return Builder
     */
    public function searchBy(Builder $query, Model $model): Builder;
}