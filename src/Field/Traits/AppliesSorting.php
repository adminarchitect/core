<?php

namespace Terranet\Administrator\Field\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait AppliesSorting
{
    /**
     * @param Builder $query;
     * @param Model $model
     * @param string $direction
     *
     * @return Builder
     */
    public function sortBy(Builder $query, Model $model, string $direction): Builder
    {
        return $query->orderBy("{$model->getTable()}.{$this->id()}", $direction);
    }
}
