<?php

namespace Terranet\Administrator\Field\Traits;

trait WorksWithModules
{
    /**
     * Finds first module which uses a model.
     *
     * @param $model
     *
     * @return mixed
     */
    public function firstWithModel($model)
    {
        return app('scaffold.modules')->first(function ($module) use ($model) {
            if (is_string($model)) {
                return get_class($module->model()) === $model;
            }

            return get_class($module->model()) === get_class($model);
        });
    }
}
