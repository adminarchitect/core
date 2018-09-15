<?php

namespace Terranet\Administrator\Contracts;

use Terranet\Administrator\Collection\Mutable;

interface Filter
{
    /**
     * Set filters.
     *
     * @param Mutable $filters
     *
     * @return mixed
     */
    public function setFilters(Mutable $filters = null);

    /**
     * Set scopes.
     *
     * @param array $scopes
     *
     * @return mixed
     */
    public function setScopes(Mutable $scopes = null);

    /**
     * Get Filters.
     *
     * @return mixed
     */
    public function filters();

    /**
     * Get scopes.
     *
     * @return mixed
     */
    public function scopes();
}
