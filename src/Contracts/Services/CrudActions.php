<?php

namespace Terranet\Administrator\Contracts\Services;

interface CrudActions
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param string $method
     * @param $eloquent
     *
     * @return bool
     */
    public function authorize($method, $eloquent = null);

    /**
     * List of single actions
     *
     * @return array
     */
    public function actions();

    /**
     * List of batch actions.
     *
     * @return mixed
     */
    public function batchActions();
}
