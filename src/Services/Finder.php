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
        // prevent duplicated execution
        if (null === $this->query && $this->model) {
            $this->initQuery()
                 ->applyFilters()
                 ->applySorting();

            $this->query = $this->assembler()->getQuery();
        }

        return $this->query;
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
     * Extend query with Order By Statement.
     */
    protected function applySorting()
    {
        $sortable = app('scaffold.sortable');
        $element = $sortable->element();
        $direction = $sortable->direction();

        if ($element && $direction) {
            if (\is_string($element)) {
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
}
