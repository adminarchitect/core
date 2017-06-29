<?php

namespace Terranet\Administrator\Filters;

use function admin\db\scheme;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Terranet\Administrator\Contracts\Form\Queryable;
use Terranet\Administrator\Contracts\QueryBuilder;
use Terranet\Administrator\Form\FormElement;
use Terranet\Administrator\Schema;
use Terranet\Translatable\Translatable;

class Assembler
{
    /**
     * @var Model model
     */
    protected $model;

    /**
     * @var Builder
     */
    protected $query;

    /**
     * @param $eloquent
     */
    public function __construct($eloquent)
    {
        $this->model = $eloquent;

        $this->query = $this->model->newQuery();
        $this->query->select($this->model->getTable() . '.*');
    }

    /**
     * Apply filters.
     *
     * @param Collection $filters
     *
     * @return $this
     */
    public function filters(Collection $filters)
    {
        foreach ($filters as $element) {
            // do not apply filter if no request var were found.
            if (!app('request')->has($element->id())) {
                continue;
            }

            $this->assemblyQuery($element);
        }

        return $this;
    }

    /**
     * Apply scope.
     *
     * @param $scope
     *
     * @return $this
     */
    public function scope(Scope $scope)
    {
        if (is_callable($callable = $scope->getQuery())) {
            list($object, $method) = $callable;

            # @note: We don't use call_user_func_array() here
            # because of missing columns in returned query.
            $this->query = $object->{"scope{$method}"}($this->query);
        }

        return $this;
    }

    /**
     * Apply ordering.
     *
     * @param $element
     * @param $direction
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function sort($element, $direction)
    {
        # simple sorting
        if (in_array($element, $sortable = app('scaffold.module')->sortable())) {
            if ($table = app('scaffold.module')->model()->getTable()) {
                $element = "{$table}.{$element}";
            }

            $this->query->orderBy($element, $direction);
        }

        if (array_key_exists($element, $sortable) && is_callable($callback = $sortable[$element])) {
            $this->query = call_user_func_array($callback, [$this->query, $element, $direction]);
        }

        if (array_key_exists($element, $sortable) && is_string($handler = $sortable[$element])) {
            $handler = new $handler($this->query, $element, $direction);
            if (!$handler instanceof QueryBuilder || !method_exists($handler, 'build')) {
                throw new \Exception('Handler class must implement ' . QueryBuilder::class . ' contract');
            }
            $this->query = $handler->build();
        }

        return $this;
    }

    /**
     * Return assembled query.
     *
     * @return Builder
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param FormElement $element
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     *
     * @throws Exception
     */
    protected function assemblyQuery(FormElement $element)
    {
        $table = $this->model->getTable();

        $input = $element->getInput();
        $type = $input->getType();
        $name = $element->id();
        $value = $input->getValue();

        $columns = scheme()->columns($table);

        if ($input->hasQuery() && ($subQuery = $input->execQuery($this->query, $input->getValue()))) {
            $this->query = $subQuery;

            return $this->query;
        }

        if ($this->touchedTranslatableFilter($input, $columns)) {
            return $this->filterByTranslatableColumn($name, $type, $value);
        }

        if (!array_key_exists($name, $columns) && $input->hasRelation()) {
            if (($relation = call_user_func([$this, $input->getRelation()])) instanceof HasOne
                || $relation instanceof BelongsTo
            ) {
                return $this->filterRelationShipColumn($input, $relation, $name, $type, $value);
            }
        }

        $this->query = $this->applyQueryElementByType($this->query, $table, $name, $type, $value);

        return $this->query;
    }

    protected function applyQueryElementByType(Builder $query, $table, $column, $type, $value = null)
    {
        switch ($type) {
            case 'text':
            case 'datalist':
                $query->where("{$table}.{$column}", 'LIKE', "%{$value}%");
                break;

            case 'select':
            case 'multiselect':
                if (!is_array($value)) {
                    $value = [$value];
                }
                $query->whereIn("{$table}.{$column}", $value);
                break;

            case 'boolean':
            case 'number':
                $query->where("{$table}.{$column}", '=', (int) $value);
                break;

            case 'date':
                $query->whereDate("{$table}.{$column}", '=', $value);
                break;

            case 'daterange':
                list($date_from, $date_to) = explode(' - ', $value);
                $query->whereBetween(\DB::raw("DATE({$table}.{$column})"), [$date_from, $date_to]);
                break;
        }

        return $query;
    }

    /**
     * @param $columns
     *
     * @return array
     */
    protected function translatableColumns(array $columns = [])
    {
        return array_except(
            scheme()->columns($this->model->getTranslationModel()->getTable()),
            array_keys($columns)
        );
    }

    /**
     * @param Queryable $input
     * @param $translatable
     *
     * @return bool
     */
    protected function isTranslatable(Queryable $input, array $translatable = [])
    {
        return array_key_exists($input->getName(), $translatable);
    }

    /**
     * @param $name
     * @param $type
     * @param $value
     *
     * @return Builder
     */
    protected function filterByTranslatableColumn($name, $type, $value)
    {
        $translation = $this->model->getTranslationModel();

        return $this->query->whereHas('translations', function ($query) use ($translation, $name, $type, $value) {
            return $query = $this->applyQueryElementByType($query, $translation->getTable(), $name, $type, $value);
        });
    }

    /**
     * @param Queryable $input
     * @param $relation
     * @param $name
     * @param $type
     * @param $value
     *
     * @return Builder
     */
    protected function filterRelationShipColumn(Queryable $input, $relation, $name, $type, $value)
    {
        $relatedTable = $relation->getRelated()->getTable();

        return $this->query->whereHas(
            $input->getRelation(),
            function ($query) use ($relatedTable, $name, $type, $value) {
                $query = $this->applyQueryElementByType($query, $relatedTable, $name, $type, $value);

                return $query;
            }
        );
    }

    /**
     * @param Queryable $input
     * @param $columns
     *
     * @return bool
     */
    protected function touchedTranslatableFilter(Queryable $input, $columns)
    {
        return ($this->model instanceof Translatable)
            && $this->isTranslatable($input, $this->translatableColumns($columns));
    }
}
