<?php

namespace Terranet\Administrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Architect
{
    /**
     * Get the AdminArchitect URI path.
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    public static function path()
    {
        return config('administrator.prefix', 'cms');
    }

    /**
     * Finds first module which uses a model.
     *
     * @param Model|string $model
     * @param string $urlKey
     *
     * @return mixed
     */
    public static function resourceByEntity($model, string $urlKey = null)
    {
        return app('scaffold.modules')->first(function ($module) use ($model, $urlKey) {
            $urlEquals = $urlKey ? $module->url() === $urlKey : true;

            if (\is_string($model)) {
                return \get_class($module->model()) === $model && $urlEquals;
            }

            return \get_class($module->model()) === \get_class($model) && $urlEquals;
        });
    }

    /**
     * Humanize the given value into a proper name.
     *
     * @param string $value
     *
     * @return string
     */
    public static function humanize($value)
    {
        if (\is_object($value)) {
            return static::humanize(class_basename(\get_class($value)));
        }

        return str_replace('_', ' ', Str::title(Str::snake($value, ' ')));
    }

    /**
     * @return ArchitectRoutes
     */
    public static function routes()
    {
        return new ArchitectRoutes();
    }
}
