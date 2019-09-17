<?php

namespace Terranet\Administrator\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Contracts\Services\Finder as FinderContract;
use Terranet\Administrator\Exception;
use Terranet\Administrator\Filters\Assembler;

class Finder implements FinderContract
{
    /** @var Module */
    protected $module;

    /** @var Model */
    protected $model;

    /** @var Builder */
    protected $query;

    /** @var Assembler */
    protected $assembler;

    /** @var Request */
    protected $request;

    /**
     * Finder constructor.
     * @param Module $module
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
        $this->model = $module->model();
        $this->request = $module->request();
    }

    /**
     * Fetch all items from repository.
     *
     * @return mixed
     * @throws Exception
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
     * @throws Exception
     */
    public function getQuery()
    {
        // prevent duplicated execution
        if (null === $this->query && $this->model) {
            $this->initQuery()
                ->applyFilters()
                ->applyRelationalFilters()
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
    protected function assembler(): Assembler
    {
        if (null === $this->assembler) {
            $this->assembler = (new Assembler($this->model));
        }

        return $this->assembler;
    }

    /**
     * @return $this
     */
    protected function initQuery(): FinderContract
    {
        if (method_exists($this->module, 'query')) {
            $this->assembler()->applyQueryCallback([$this->module, 'query']);
        }

        return $this;
    }

    /**
     * Apply query string filters.
     *
     * @return FinderContract
     * @throws Exception
     */
    protected function applyFilters(): FinderContract
    {
        if ($filter = $this->module->filter()) {
            if ($filters = $filter->filters()) {
                $this->assembler()->filters($filters);
            }

            if ($scopes = $filter->scopes()) {
                if (($scope = $filter->scope()) && ($found = $scopes->find($scope))) {
                    $this->assembler()->scope($found);
                }
            }
        }

        return $this;
    }

    /**
     * Apply relation filters: cross-links to other
     *
     * @return $this
     */
    protected function applyRelationalFilters(): FinderContract
    {
        if (($relation = request('viaResource')) && ($value = request('viaResourceId'))) {
            $this->assembler()->relations($relation, $value);
        }

        return $this;
    }

    /**
     * Extend query with Order By Statement.
     *
     * @return FinderContract
     * @throws \Exception
     */
    protected function applySorting(): FinderContract
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

    /**
     * Items per page.
     *
     * @return int
     */
    protected function perPage(): int
    {
        return method_exists($this->module, 'perPage')
            ? $this->module->perPage()
            : 20;
    }
}
