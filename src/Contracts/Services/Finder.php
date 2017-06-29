<?php

namespace Terranet\Administrator\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;

interface Finder
{
    /**
     * Fetch all items from repository.
     *
     * @return Collection
     */
    public function fetchAll();

    /**
     * Find a record by id.
     *
     * @param       $key
     * @param array $columns
     *
     * @return mixed
     */
    public function find($key, $columns = ['*']);
}
