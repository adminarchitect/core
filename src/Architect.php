<?php

namespace Terranet\Administrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Architect
{
    /**
     * Finds first module which uses a model.
     *
     * @param string|Model $model
     *
     * @return mixed
     */
    public static function resourceByEntity($model)
    {
        return app('scaffold.modules')->first(function ($module) use ($model) {
            if (is_string($model)) {
                return get_class($module->model()) === $model;
            }

            return get_class($module->model()) === get_class($model);
        });
    }

    /**
     * Humanize the given value into a proper name.
     *
     * @param  string $value
     * @return string
     */
    public static function humanize($value)
    {
        if (is_object($value)) {
            return static::humanize(class_basename(get_class($value)));
        }

        return Str::title(Str::snake($value, ' '));
    }
}