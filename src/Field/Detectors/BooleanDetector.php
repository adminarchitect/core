<?php

namespace Terranet\Administrator\Field\Detectors;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\BooleanType;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Field\Boolean;

class BooleanDetector extends AbstractDetector
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
        return $metadata->getType() instanceof BooleanType;
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
        return Boolean::class;
    }
}
