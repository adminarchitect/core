<?php

namespace Terranet\Administrator\Decorators;

use Czim\Paperclip\Contracts\AttachableInterface;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Chain;
use Terranet\Administrator\Field\Detectors\BooleanDetector;
use Terranet\Administrator\Field\Detectors\DateTimeDetector;
use Terranet\Administrator\Field\Detectors\EmailDetector;
use Terranet\Administrator\Field\Detectors\EnumDetector;
use Terranet\Administrator\Field\Detectors\LinkDetector;
use Terranet\Administrator\Field\Detectors\PasswordDetector;
use Terranet\Administrator\Field\Detectors\PhoneDetector;
use Terranet\Administrator\Field\Detectors\PrimaryKeyDetector;
use Terranet\Administrator\Field\Detectors\RankableDetector;
use Terranet\Administrator\Field\Detectors\TextareaDetector;
use Terranet\Administrator\Field\Detectors\TextDetector;
use Terranet\Administrator\Field\File;
use Terranet\Administrator\Field\Field;
use Terranet\Administrator\Field\Image;
use Terranet\Administrator\Field\Text;
use Terranet\Translatable\Translatable;

class Grid
{
    /** @var Model */
    private $model;

    /**
     * Grid constructor.
     *
     * @param null|Model $model
     */
    public function __construct(Model $model = null)
    {
        $this->model = $model ?: app('scaffold.module')->model();
    }

    /**
     * @param $element
     *
     * @return mixed|\Terranet\Administrator\Field\Field
     */
    public function make($element)
    {
        // decorate attachment
        if ($this->model instanceof AttachableInterface
            && method_exists($this->model, 'getAttachedFiles')
            && array_key_exists($element, $this->model->getAttachedFiles())
        ) {
            $file = $this->model->getAttachedFiles()[$element];
            if ($file->variants()) {
                return Image::make($element, $element);
            }

            return File::make($element, $element);
        }

        // decorate translatable attachment
        if ($this->model instanceof Translatable) {
            $translation = $this->model->getTranslationModel();

            if ($translation instanceof AttachableInterface
                && method_exists($translation, 'getAttachedFiles')
                && array_key_exists($element, $translation->getAttachedFiles())
            ) {
                return Image::make($element);
            }
        }

        // decorate table column
        if ($this->realColum($element)) {
            $field = $this->detectField($element);

            if (\is_object($field)) {
                return $field;
            }

            return forward_static_call_array([$field, 'make'], [$element, $element]);
        }

        return Text::make($element, $element);
    }

    /**
     * @param $column
     *
     * @return bool
     */
    protected function realColum($column)
    {
        return array_key_exists($column, $this->fetchTablesColumns());
    }

    /**
     * @param $column
     *
     * @return Field
     */
    protected function detectField($column)
    {
        return Chain::make([
            new PrimaryKeyDetector(),
            new RankableDetector(),
            new DateTimeDetector(),
            new BooleanDetector(),
            new TextareaDetector(),
            new EnumDetector(),
            new PasswordDetector(),
            new EmailDetector(),
            new LinkDetector(),
            new PhoneDetector(),
            new TextDetector(),
        ])($column, $this->fetchTablesColumns()[$column], $this->model);
    }

    /**
     * @return array
     */
    protected function fetchTablesColumns()
    {
        return \admin\db\table_columns($this->model, true);
    }
}
