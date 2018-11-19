<?php

namespace Terranet\Administrator\Filters;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Terranet\Administrator\Contracts\Filter\Searchable;
use Terranet\Administrator\Contracts\Form\Queryable;
use Terranet\Administrator\Contracts\QueryBuilder;
use Terranet\Administrator\Contracts\Sortable;
use Terranet\Administrator\Filter\Filter;
use Terranet\Administrator\Form\FormElement;
use Terranet\Translatable\Translatable;
use function admin\db\scheme;

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
        if ($this->model = $eloquent) {
            $this->query = $this->model->newQuery();
            $this->query->select($this->model->getTable().'.*');
        }
    }

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
     * @throws \Terranet\Administrator\Exception
     *
     * @return $this
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
            if (str_contains($callable, '@')) {
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
     * Apply ordering.
     *
     * @param $element
     * @param $direction
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function sort($element, $direction)
    {
        // simple sorting
        if (\in_array($element, $sortable = app('scaffold.module')->sortable(), true)) {
            $columns = app('scaffold.module')->columns();
            $model = app('scaffold.module')->model();

            $sortable = $columns->find($element);

            // Each column may apply it's own sorting policy.
            if ($sortable instanceof Sortable) {
                $sortable->sortBy($this->query, $model, $direction);
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
     * @throws Exception
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    protected function assemblyQuery(Filter $element)
    {
        $table = $this->model->getTable();
        $value = $element->value();

        if (null === $value) {
            return $this->query;
        }

        $columns = scheme()->columns($table);

        // Filters with a custom query
        if ($element instanceof Queryable && $element->hasQuery()) {
            return $this->query = $element->execQuery($this->query, $value);
        }

        if ($this->touchedTranslatableFilter($element, $columns)) {
            return $this->filterByTranslatableColumn($element);
        }

        // Basic filters
        if ($element instanceof Searchable) {
            $this->query = $element->searchBy($this->query, $this->model);
        }

        return $this->query;
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
     * @param mixed $element
     *
     * @return Builder
     */
    protected function filterByTranslatableColumn($element)
    {
        $translation = $this->model->getTranslationModel();

        dd('@todo', __METHOD__);

        return $this->query->whereHas('translations', function ($query) use ($translation, $name, $type, $value) {
            return $query = $this->applyQueryElementByType($query, $translation->getTable(), $name, $type, $value);
        });
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
