<?php

namespace Terranet\Administrator\Field\Detectors;

use BenSampo\Enum\Traits\CastsEnums;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\BooleanType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Terranet\Administrator\Field\Boolean;
use Terranet\Administrator\Field\Enum;
use Zend\Code\Reflection\ClassReflection;

class CastedEnumDetector extends AbstractDetector
{
    /**
     * Authorize execution.
     *
     * @param  string  $column
     * @param  Column  $metadata
     * @param  Model  $model
     *
     * @return bool
     */
    protected function authorize(string $column, Column $metadata, Model $model): bool
    {
        return Arr::has(class_uses($model), CastsEnums::class) && $model->hasEnumCast($column);
    }

    /**
     * Detect field class.
     *
     * @param  string  $column
     * @param  Column  $metadata
     * @param  Model  $model
     *
     * @return mixed
     */
    protected function detect(string $column, Column $metadata, Model $model)
    {
        $reflection = new ClassReflection($model);

        $property = tap($reflection->getProperty('enumCasts'))->setAccessible(true);

        $enum = $property->getValue(new $model)[$column];

        $values = $enum::getKeys();
        if (!$metadata->getNotNull()) {
            $values = ['' => '----'] + $values;
        }

        return Enum::make($column)->setOptions($values);
    }
}
