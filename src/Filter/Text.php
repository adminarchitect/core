<?php

namespace Terranet\Administrator\Filter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Terranet\Administrator\Contracts\Filter\Searchable;
use Illuminate\Support\Facades\Request;

class Text extends Filter implements Searchable
{
    /**
     * Whether to enable modes or not.
     *
     * @var bool
     */
    protected $withModes = true;

    /**
     * @return array
     */
    protected function renderWith()
    {
        return [
            'modes' => $this->withModes ? trans('administrator::buttons.search_modes') : []
        ];
    }

    /**
     * @return $this
     */
    public function disableModes()
    {
        $this->withModes = false;

        return $this;
    }

    /**
     * @param Builder $query
     * @param Model $model
     * @return Builder|void
     */
    public function searchBy(Builder $query, Model $model): Builder
    {
        $modeName = $this->name().'_mode';
        $mode = Request::get($modeName, 'equals');

        $modeMap = [
            'equals' => ['=', $this->value()],
            'not_equals' => ['<>', $this->value()],
            'starts_with' => ['LIKE', "{$this->value()}%"],
            'ends_with' => ['LIKE', "%{$this->value()}"],
            'contains' => ['LIKE', "%{$this->value()}%"],
        ];

        [$operator, $value] = $modeMap[$mode];

        return $query->where("{$model->getTable()}.{$this->id()}", $operator, $value);
    }
}