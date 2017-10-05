<?php

namespace Terranet\Administrator\Traits\Module;

use function admin\db\connection;
use function admin\db\enum_values;
use function admin\db\scheme;
use Codesleeve\Stapler\ORM\StaplerableInterface;
use DB;
use Doctrine\DBAL\Schema\Column;
use Terranet\Administrator\Form\Collection\Mutable;
use Terranet\Administrator\Form\FormElement;
use Terranet\Administrator\Form\InputFactory;
use Terranet\Administrator\Form\Type\Select;
use Terranet\Translatable\Translatable;

trait HasForm
{
    protected $typesMap = [
        'text' => ['StringType', 'GuidType'],
        'textarea' => ['TextType'],
        'boolean' => ['BooleanType'],
        'number' => ['IntegerType', 'BigIntType', 'DecimalType', 'FloatType'],
        'datetime' => ['DateTimeType', 'DateTimeTzType'],
        'date' => ['DateType'],
    ];

    /**
     * Provides array of editable columns
     *
     * @return Mutable
     */
    public function form()
    {
        return $this->scaffoldForm();
    }

    /**
     * Build editable columns based on table columns metadata
     *
     * @return Mutable
     */
    protected function scaffoldForm()
    {
        $editable = new Mutable;

        if ($eloquent = $this->model()) {
            $editable = $editable->merge($translatable = $this->scaffoldTranslatable($eloquent))
                                 ->merge($eloquent->getFillable());
            
            return $editable->map(function ($name) use ($eloquent, $translatable) {
                $formElement = InputFactory::make(
                    $name, $this->inputType($name, $eloquent)
                );

                if (in_array($name, $translatable)) {
                    $formElement->setTranslatable(true);
                }

                # For Enums (Select|Radio) try to extract values from database.
                if (is_a($formElement, Select::class)
                    && empty($formElement->getOptions())
                    && connection('mysql')
                ) {
                    $formElement->setOptions(
                        $values = enum_values($table = $eloquent->getTable(), $name)
                    );

                    # set appropriate styling
                    if (count($values) > 5) {
                        $formElement->setStyle([
                            'display' => 'block',
                        ]);
                    }

                    # set default value
                    if ($default = scheme()->columns($table)[$name]->getDefault()) {
                        $formElement->setValue($default);
                    }
                }

                $container = new FormElement($name);

                return $container->setInput($formElement);
            });
        }

        return $editable;
    }

    /**
     * @param $column
     * @param $eloquent
     * @return string
     */
    protected function inputType($column, $eloquent)
    {
        static $columns = [];

        if (method_exists($this, 'inputTypes')
            && array_key_exists($column, $types = $this->inputTypes())
        ) {
            return $types[$column];
        }

        if ($eloquent instanceof StaplerableInterface
            && array_key_exists($column, $attachments = $eloquent->getAttachedFiles())
        ) {
            return $this->getAttachmentType($column, $attachments);
        }

        $columns = array_merge($columns, $this->allColumns($eloquent));

        # map column database type to input type
        if ($column = array_get($columns, $column)) {
            return $this->mapColumnTypeToFieldType($column);
        }

        if ($chain = $this->isRelationColumn($column)) {
            list($relColumn, $table) = $this->resolveRelationsChain($chain, $eloquent);

            return $this->inputType($relColumn, $table->getRelated());
        }

        return 'text';
    }

    protected function resolveRelationsChain($chain, $eloquent)
    {
        $relColumn = array_pop($chain);

        $table = array_reduce($chain, function ($table, $object) use ($eloquent) {
            $table = $table ?: $eloquent;

            return $table = call_user_func([$table, $object]);
        }, null);

        return [$relColumn, $table];
    }

    /**
     * @param Column $column
     * @return int|string
     */
    protected function mapColumnTypeToFieldType(Column $column)
    {
        foreach ($this->typesMap as $type => $classes) {
            if (in_array($typeClass = $this->columnType($column), $classes)) {
                if ('StringType' == $typeClass) {
                    if (connection('mysql') && null === $column->getLength()) {
                        return 'radio';
                    }
                }

                return $type;
            }
        }

        return 'text';
    }

    /**
     * @param $column
     * @return string
     */
    protected function columnType(Column $column)
    {
        return class_basename($column->getType());
    }

    /**
     * @param $column
     * @param $attachments
     * @return array
     */
    protected function getAttachmentType($column, $attachments)
    {
        return count($attachments[$column]->getConfig()->styles) <= 1 ? 'file' : 'image';
    }

    protected function scaffoldTranslatable($eloquent)
    {
        return ($eloquent instanceof Translatable && method_exists($eloquent, 'getTranslatedAttributes'))
            ? $eloquent->getTranslatedAttributes()
            : [];
    }

    /**
     * @param $eloquent
     * @return array
     */
    protected function allColumns($eloquent)
    {
        static $columns = [];

        $table = is_object($eloquent) ? $eloquent->getTable() : $eloquent;

        if (!array_has($columns, $table) || empty($columns)) {
            $columns[$table] = scheme()->columns($table);

            if ($eloquent instanceof Translatable && method_exists($eloquent, 'getTranslationModel')) {
                $translationModel = $eloquent->getTranslationModel();

                $columns[$table] = array_merge(
                    $columns[$table],
                    scheme()->columns($translationModel->getTable())
                );
            }
        }

        return array_get($columns, $table, []);
    }

    /**
     * @param $column
     * @return bool
     */
    protected function isRelationColumn($column)
    {
        return count($chain = explode('.', $column)) > 1 ? $chain : false;
    }
}
