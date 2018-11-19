<?php

namespace Terranet\Administrator\Field\Detectors;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\TextType;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Field\Textarea;

class TextareaDetector extends AbstractDetector
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
        return $metadata->getType() instanceof TextType;
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
        return Textarea::class;
    }
}
