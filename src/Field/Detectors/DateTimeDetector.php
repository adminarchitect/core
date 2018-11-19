<?php

namespace Terranet\Administrator\Field\Detectors;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\TimeType;
use Illuminate\Database\Eloquent\Model;

class DateTimeDetector extends AbstractDetector
{
    /**
     * Authorize execution.
     *
     * @param string $column
     * @param Column $metadata
     * @param Model $model
     *
     * @return bool
     */
    protected function authorize(string $column, Column $metadata, Model $model): bool
    {
        return \in_array(\get_class($metadata->getType()), [
            TimeType::class,
            DateType::class,
            DateTimeType::class,
        ], true);
    }

    /**
     * Detect field class.
     *
     * @param string $column
     * @param Column $metadata
     * @param Model $model
     *
     * @return mixed
     */
    protected function detect(string $column, Column $metadata, Model $model)
    {
        $className = class_basename($metadata->getType());

        $type = str_replace('Type', '', $className);

        return "\\Terranet\\Administrator\\Field\\{$type}";
    }
}
