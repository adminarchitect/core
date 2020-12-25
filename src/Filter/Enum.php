<?php

namespace Terranet\Administrator\Filter;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Contracts\Filter\Searchable;
use Terranet\Administrator\Traits\Form\SupportsListTypes;

class Enum extends Filter implements Searchable
{
    use SupportsListTypes;

    /** @var array */
    public $options = [];
    /** @var string */
    protected $component = 'enum';

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
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed array|\Closure $options
     * @return self
     * @throws Exception
     */
    public function setOptions($options): self
    {
        if (!(\is_array($options) || $options instanceof \Closure)) {
            throw new Exception('Enum accepts only `array` or `Closure` as options.');
        }

        if ($options instanceof \Closure) {
            $options = \call_user_func_array($options, []);

            return $this->setOptions($options);
        }

        $this->options = $options;

        return $this;
    }
}
