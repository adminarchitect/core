<?php

namespace Terranet\Administrator\Decorators;

use function admin\db\connection;
use function admin\db\enum_values;
use function admin\db\translated_values;
use Czim\Paperclip\Contracts\AttachableInterface;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Chain;
use Terranet\Administrator\Field\Boolean;
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
use Terranet\Administrator\Field\Email;
use Terranet\Administrator\Field\Enum;
use Terranet\Administrator\Field\File;
use Terranet\Administrator\Field\Generic;
use Terranet\Administrator\Field\Id;
use Terranet\Administrator\Field\Image;
use Terranet\Administrator\Field\Link;
use Terranet\Administrator\Field\Password;
use Terranet\Administrator\Field\Phone;
use Terranet\Administrator\Field\Rank;
use Terranet\Administrator\Field\Text;
use Terranet\Administrator\Field\Textarea;
use Terranet\Rankable\Rankable;
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
     * @return mixed|\Terranet\Administrator\Field\Generic
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

            if (is_object($field)) {
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
     * @return Generic
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
