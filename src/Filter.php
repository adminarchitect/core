<?php

namespace Terranet\Administrator;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Terranet\Administrator\Collection\Mutable;
use Terranet\Administrator\Contracts\Filter as FilterContract;
use Terranet\Administrator\Filters\Scope;

class Filter implements FilterContract
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * Filters collection.
     *
     * @var null|Collection
     */
    protected $filters;

    /**
     * Scopes collection.
     *
     * @var null|Collection
     */
    protected $scopes;

    /**
     * @param Request $request
     * @param Mutable $filters
     * @param Mutable $scopes
     */
    public function __construct(Request $request, Mutable $filters = null, Mutable $scopes = null)
    {
        $this->request = $request;

        $this->setFilters($filters);
        $this->setScopes($scopes);
    }

    /**
     * Set collection of filters.
     *
     * @param null|Mutable $filters
     *
     * @return mixed|void
     */
    public function setFilters(Mutable $filters = null)
    {
        if ($filters) {
            $filters = $filters->map(function ($element) {
                if ($this->request->has($id = $element->id())) {
                    $element->setValue(
                        $this->request->get($id)
                    );
                }

                return $element;
            });
        }

        $this->filters = $filters;

        return $this;
    }

    /**
     * Set scopes.
     *
     * @param Mutable $scopes
     *
     * @return $this
     */
    public function setScopes(Mutable $scopes = null)
    {
        if ($scopes && $scopes->count()) {
            $scopes->prepend(
                new Scope('all')
            );
        }

        $this->scopes = $scopes;

        return $this;
    }

    /**
     * Get all filters.
     *
     * @return null|Collection
     */
    public function filters()
    {
        return $this->filters;
    }

    /**
     * Get scopes.
     *
     * @return mixed
     */
    public function scopes()
    {
        return $this->scopes;
    }

    /**
     * Get current scope.
     *
     * @return mixed
     */
    public function scope()
    {
        return $this->request->get('scoped_to', null);
    }

    /**
     * Build an url to desired scope.
     *
     * @param null $scope
     *
     * @return string
     */
    public function makeScopedUrl($scope = null)
    {
        return \admin\helpers\qsRoute(null, [
            'scoped_to' => $scope,
            'page' => 1,
        ]);
    }

    /**
     * Check if filter has element with name.
     *
     * @param $name
     *
     * @return bool
     */
    public function has($name)
    {
        foreach ($this->filters() as $filter) {
            if ($filter->id() === $name) {
                return true;
            }
        }

        return false;
    }
}
