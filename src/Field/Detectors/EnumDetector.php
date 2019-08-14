<?php

namespace Terranet\Administrator\Field\Detectors;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\StringType;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Field\Enum;
use function admin\db\connection;
use function admin\db\enum_values;
use function admin\db\translated_values;

class EnumDetector extends AbstractDetector
{
    /** @var array */
    protected $values = [];

    /**
     * @param  string  $column
     * @param  Model  $model
     * @return mixed
     */
    protected function enumValues(string $column, Model $model)
    {
        return $this->values = enum_values($model->getTable(), $column);
    }

    /**
     * Authorize execution.
     *
     * @param  string  $column
     * @param  Column  $metadata
     * @param  Model  $model
     * @return bool
     */
    protected function authorize(string $column, Column $metadata, Model $model): bool
    {
        return connection('mysql')
            && $metadata->getType() instanceof StringType
            && 0 === (int) $metadata->getLength()
            && !empty($this->enumValues($column, $model));
    }

    /**
     * Detect field class.
     *
     * @param  string  $column
     * @param  Column  $metadata
     * @param  Model  $model
     * @return mixed
     */
    protected function detect(string $column, Column $metadata, Model $model)
    {
        $values = translated_values($this->values, app('scaffold.module')->url(), $column);

        if (!$metadata->getNotNull()) {
            $values = ['' => '----'] + $values;
        }

        return Enum::make($column, $column)->setOptions($values);
    }
}
