<?php

namespace Terranet\Administrator;

use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Zend\Code\Reflection\ClassReflection;

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
     * @param  Model|string  $model
     * @param  string  $urlKey
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
     * Finds resource by a key.
     *
     * @param  string  $url
     * @return mixed
     */
    public static function resourceForKey(string $url)
    {
        return app('scaffold.modules')->first(function ($module) use ($url) {
            return $module->url() === $url;
        });
    }

    /**
     * Humanize the given value into a proper name.
     *
     * @param  string  $value
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

    /**
     * Ensure the column is of type Enum.
     *
     * @param  Model  $model
     * @param  string  $column
     * @return bool
     */
    public static function castedEnumType(Model $model, string $column)
    {
        return Arr::has(class_uses($model), CastsEnums::class) && $model->hasEnumCast($column);
    }

    /**
     * Extract values from Casted Enum type.
     *
     * @param  Model  $model
     * @param  string  $column
     * @param  bool  $nullable
     * @return array
     * @throws \ReflectionException
     */
    public static function castedEnumValues(Model $model, string $column, bool $nullable = false)
    {
        $reflection = new ClassReflection($model);

        $property = tap($reflection->getProperty('enumCasts'))->setAccessible(true);

        $enum = $property->getValue(new $model)[$column];

        $values = $enum::toSelectArray();
        if ($nullable) {
            $values = ['' => '----'] + $values;
        }

        return $values;
    }
}
