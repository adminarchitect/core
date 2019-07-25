<?php

namespace Terranet\Administrator\Field\Detectors;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Types\DecimalType;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\SmallIntType;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Field\Number;
use Terranet\Administrator\Field\Text;

class NumberDetector extends AbstractDetector
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
        $class = get_class($metadata->getType());

        return in_array($class, [
            IntegerType::class,
            DecimalType::class,
            FloatType::class,
            BigIntType::class,
            SmallIntType::class,
        ]);
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
        return Number::class;
    }
}
