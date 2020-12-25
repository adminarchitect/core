<?php

namespace Terranet\Administrator\Filter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Terranet\Administrator\Contracts\Filter\Searchable;
use function localizer\locale;

class Text extends Filter implements Searchable
{
    /**
     * Whether to enable search modes or not.
     *
     * @var bool
     */
    public $withModes = false;

    /** @var string */
    protected $component = 'text';

    /**
     * @return $this
     */
    public function enableModes(): self
    {
        $this->withModes = true;

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
        $modeName = $this->name().'_mode';
        $mode = Request::get($modeName, 'contains');

        $modeMap = [
            'equals' => ['=', $this->value()],
            'not_equals' => ['!=', $this->value()],
            'starts_with' => ['LIKE', "{$this->value()}%"],
            'ends_with' => ['LIKE', "%{$this->value()}"],
            'contains' => ['LIKE', "%{$this->value()}%"],
            'not_contains' => ['NOT LIKE', "%{$this->value()}%"],
        ];

        [$operator, $value] = $modeMap[$mode];

        if ($this->shouldSearchInTranslations($model)) {
            $translation = $model->getTranslationModel();

            return $query->whereHas('translations', function ($query) use ($translation, $value, $operator) {
                $query->where("{$translation->getTable()}.language_id", locale()->id());

                return $query->where("{$translation->getTable()}.{$this->name()}", $operator, $value);
            });
        }

        return $query->where("{$model->getTable()}.{$this->id()}", $operator, $value);
    }

    protected function renderWith(): array
    {
        $mods = $this->withModes ? trans('administrator::buttons.search_modes') : [];

        return compact('mods');
    }
}
