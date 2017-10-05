<?php

namespace Terranet\Administrator\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Terranet\Administrator;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Contracts\Services\Finder as FinderContract;
use Terranet\Administrator\Filters\Assembler;
use Terranet\Administrator\Filters\InputFactory;
use Terranet\Administrator\Form\FormElement;

class Finder implements FinderContract
{
    /**
     * @var Module
     */
    protected $module;

    /**
     * @var
     */
    protected $model;

    /**
     * @var Builder
     */
    protected $query;

    /**
     * Query assembler.
     *
     * @var
     */
    protected $assembler;

    public function __construct(Module $module)
    {
        $this->module = $module;
        $this->model = $module->model();
    }

    /**
     * Fetch all items from repository.
     *
     * @return mixed
     */
    public function fetchAll()
    {
        if ($query = $this->getQuery()) {
            return $query->paginate($this->perPage());
        }

        return new LengthAwarePaginator([], 0, 10, 1);
    }

    /**
     * Build Scaffolding Index page query.
     *
     * @return mixed
     */
    public function getQuery()
    {
        # prevent duplicated execution
        if (null === $this->query && $this->model) {
            $this->initQuery()
                 ->applyFilters()
                 ->applySorting();

            $this->query = $this->assembler()->getQuery();
        }

        return $this->query;
    }

    /**
     * Get the query assembler object.
     *
     * @return Assembler
     */
    protected function assembler()
    {
        if (null === $this->assembler) {
            $this->assembler = (new Assembler($this->model));
        }

        return $this->assembler;
    }

    protected function initQuery()
    {
        if (method_exists($this->module, 'query')) {
            $this->assembler()->applyQueryCallback([$this->module, 'query']);
        }

        return $this;
    }

    protected function applyFilters()
    {
        if ($filter = app('scaffold.filter')) {
            if ($filters = $filter->filters()) {
                $this->assembler()->filters($filters);
            }

            if ($magnet = app('scaffold.magnet')) {
                if ($filters && count($filters)) {
                    $magnet = $this->removeDuplicates($magnet, $filters);
                }

                $this->applyMagnetFilter($magnet);
            }

            if ($scopes = $filter->scopes()) {
                if (($scope = $filter->scope()) && ($found = $scopes->find($scope))) {
                    $this->assembler()->scope(
                        $found
                    );
                }
            }
        }

        return $this;
    }

    /**
     * Solving problem then magnet params gets used for auto-filtering
     * even if there is another filter defined with the same name
     *
     * @param $magnet
     * @param $filters
     * @return array
     */
    protected function removeDuplicates($magnet, $filters)
    {
        $magnetKeys = $magnet->toArray();

        foreach ($filters as $filter) {
            if (array_has($magnetKeys, $filter->id())) {
                unset($magnetKeys[$filter->id()]);
            }
        }

        $class = get_class(app('scaffold.magnet'));

        return new $class(app('request'), $magnetKeys);
    }

    /**
     * Auto-scope fetching items to magnet parameter.
     *
     * @param MagnetParams $magnet
     */
    protected function applyMagnetFilter(MagnetParams $magnet)
    {
        $filters = new Administrator\Form\Collection\Mutable;

        foreach ($magnet->toArray() as $key) {
            $element = new FormElement($key);
            $element->setInput(
                InputFactory::make($key, 'text')
            );
            $filters->push($element);
        }

        $magnetFilters = new Administrator\Filter(
            app('request'),
            $filters
        );

        $this->assembler()->filters($magnetFilters->filters());
    }

    /**
     * Extend query with Order By Statement.
     */
    protected function applySorting()
    {
        $sortable = app('scaffold.sortable');
        $element = $sortable->element();
        $direction = $sortable->direction();

        if ($element && $direction) {
            if (is_string($element)) {
                $this->assembler()->sort($element, $direction);
            }
        }

        return $this;
    }

    protected function perPage()
    {
        return method_exists($this->module, 'perPage')
            ? $this->module->perPage()
            : 20;
    }

    /**
     * Find a record by id or fail.
     *
     * @param       $key
     * @param array $columns
     *
     * @return mixed
     */
    public function find($key, $columns = ['*'])
    {
        $this->model = $this->model->newQueryWithoutScopes()->findOrFail($key, $columns);

        return $this->model;
    }
}
