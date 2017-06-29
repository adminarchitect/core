<?php

namespace Terranet\Administrator\Contracts;

interface ActionsManager
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param string $method
     * @param $model
     *
     * @return bool
     */
    public function authorize($method, $model = null);

    /**
     * Parse given class for single actions.
     *
     * @return array
     */
    public function actions();

    /**
     * Call handler method.
     *
     * @param       $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function exec($method, array $arguments = []);
}
