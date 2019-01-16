<?php

namespace Terranet\Administrator\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ActionsManager
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param string $method
     * @param null|Model $model
     *
     * @return bool
     */
    public function authorize($method, ?Model $model = null);

    /**
     * Parse given class for single actions.
     *
     * @return array
     */
    public function actions();

    /**
     * Call handler method.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function exec(string $method, array $arguments = []);
}
