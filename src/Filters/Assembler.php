<?php

namespace Terranet\Administrator\Filters;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Terranet\Administrator\Contracts\Filter\Searchable;
use Terranet\Administrator\Contracts\Form\Queryable;
use Terranet\Administrator\Contracts\QueryBuilder;
use Terranet\Administrator\Contracts\Sortable;
use Terranet\Administrator\Field\Traits\HandlesRelation;
use Terranet\Administrator\Filter\Filter;
use Terranet\Administrator\Form\FormElement;

class Assembler
{
    use HandlesRelation;

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
        if ($this->model = $eloquent) {
            $this->query = $this->model->newQuery();
            $this->query->select($this->model->getTable().'.*');
        }
    }

    /**
     * @param $callback
     * @return $this
     */
    public function applyQueryCallback($callback)
    {
        $this->query = $callback($this->query);

        return $this;
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
     * @param Scope $scope
     *
     * @return $this
     * @throws \Terranet\Administrator\Exception
     *
     */
    public function scope(Scope $scope)
    {
        $callable = $scope->getQuery();

        if (\is_string($callable)) {
            /*
             * Adds a Class "ClassName::class" syntax.
             *
             * @note In this case Query class should implement Contracts\Module\Queryable interface.
             * @example: (new Scope('name'))->setQuery(Queryable::class);
             */
            if (class_exists($callable)) {
                $object = app($callable);

                if (!method_exists($object, 'query')) {
                    throw new \Terranet\Administrator\Exception(
                        sprintf(
                            'Query object %s should implement %s interface',
                            \get_class($object),
                            \Terranet\Administrator\Contracts\Module\Queryable::class
                        )
                    );
                }

                $this->query = $object->query($this->query);

                return $this;
            }

            /*
             * Allows "SomeClass@method" syntax.
             *
             * @example: (new Scope('name'))->addQuery("User@active")
             */
            if (Str::contains($callable, '@')) {
                [$object, $method] = explode('@', $callable);

                $this->query = app($object)->$method($this->query);

                return $this;
            }
        }

        /*
         * Allows adding a \Closure as a query;
         *
         * @example: (new Scope('name'))->setQuery(function($query) { return $this->modify(); })
         */
        if ($callable instanceof Closure) {
            $this->query = $callable($this->query);

            return $this;
        }

        /*
         * Accepts callable builder
         *
         * @example: (new Scope('name'))->setQuery([SomeClass::class, "queryMethod"]);
         */
        if (\is_callable($callable)) {
            [$object, $method] = $callable;

            if (\is_string($object)) {
                $object = app($object);
            }

            // Call Model Scope immediately when detected.
            //
            // @note: We don't use call_user_func_array() here
            // because of missing columns in returned query.
            $this->query = with(
                $this->model->is($object) ? $this->query : $object
            )->{$method}($this->query);
        }

        return $this;
    }

    /**
     * @param string $viaResource
     * @param int|null $viaResourceId
     */
    public function relations(string $viaResource, int $viaResourceId)
    {
        $relation = $this->model->{$viaResource}();

        if (is_a($relation, BelongsToMany::class) || is_a($relation, BelongsTo::class)) {
            $keys = [
                BelongsToMany::class => 'getRelatedPivotKeyName',
                BelongsTo::class => 'getForeignKeyName',
            ];
            return $this->query->whereHas($viaResource, function (Builder $query) use ($relation, $viaResourceId, $keys) {
                $method = $keys[get_class($relation)];
                return $query->where(
                    $relation->$method(), $viaResourceId
                );
            });
        }

        return $this->query;
    }

    /**
     * Apply ordering.
     *
     * @param $element
     * @param $direction
     *
     * @return $this
     * @throws \Exception
     *
     */
    public function sort($element, $direction)
    {
        // simple sorting
        if (\in_array($element, $sortable = app('scaffold.module')->sortable(), true)) {
            $columns = app('scaffold.module')->columns();
            $model = app('scaffold.module')->model();

            $field = $columns->find($element);

            // Each column may apply it's own sorting policy.
            if ($field && $field instanceof Sortable) {
                $field->sortBy($this->query, $model, $direction);
            }
        }

        if (array_key_exists($element, $sortable) && \is_callable($callback = $sortable[$element])) {
            $this->query = \call_user_func_array($callback, [$this->query, $element, $direction]);
        }

        if (array_key_exists($element, $sortable) && \is_string($handler = $sortable[$element])) {
            $handler = new $handler($this->query, $element, $direction);
            if (!$handler instanceof QueryBuilder || !method_exists($handler, 'build')) {
                throw new \Exception('Handler class must implement '.QueryBuilder::class.' contract');
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
     * @throws Exception
     *
     */
    protected function assemblyQuery(Filter $element)
    {
        $value = $element->value();

        if (null === $value) {
            return $this->query;
        }

        // Filters with a custom query
        if ($element instanceof Queryable && $element->hasQuery()) {
            return $this->query = $element->execQuery($this->query, $value);
        }

        // Basic filters
        if ($element instanceof Searchable) {
            $this->query = $element->searchBy($this->query, $this->model);
        }

        return $this->query;
    }
}
