<?php

namespace Terranet\Administrator\Field\Detectors;

use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Architect;
use Terranet\Administrator\Field\Enum;

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
        return Architect::castedEnumType($model, $column, !$metadata->getNotnull());
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
        return Enum::make($column)->setOptions(
            Architect::castedEnumValues($model, $column, !$metadata->getNotNull())
        );
    }
}
