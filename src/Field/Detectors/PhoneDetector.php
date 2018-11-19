<?php

namespace Terranet\Administrator\Field\Detectors;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\StringType;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Field\Phone;

class PhoneDetector extends AbstractDetector
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
        return $metadata->getType() instanceof StringType && str_contains($column, ['phone', 'gsm']);
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
        return Phone::class;
    }
}
