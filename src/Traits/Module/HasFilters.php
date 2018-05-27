<?php

namespace Terranet\Administrator\Traits\Module;

use Terranet\Administrator\Filters\FilterElement;
use Terranet\Administrator\Filters\InputFactory as FilterInputFactory;
use Terranet\Administrator\Filters\Scope;
use Terranet\Administrator\Form\Collection\Mutable;
use function admin\db\connection;
use function admin\db\enum_values;
use function admin\db\table_columns;
use function admin\db\table_indexes;

trait HasFilters
{
    /**
     * Defined filters.
     *
     * @var Mutable
     */
    protected $filters;

    /**
     * Defined scopes.
     *
     * @var Mutable
     */
    protected $scopes;

    /**
     * Register a filter.
     *
     * @param FilterElement $filter
     *
     * @return $this
     */
    public function addFilter(FilterElement $filter)
    {
        $this->filters->push($filter);

        return $this;
    }

    /**
     * Register a scope.
     *
     * @param Scope $scope
     *
     * @return $this
     */
    public function addScope(Scope $scope)
    {
        $this->scopes->push($scope);

        return $this;
    }

    /**
     * Default list of filters.
     *
     * @return mixed
     */
    public function filters()
    {
        return $this->scaffoldFilters();
    }

    /**
     * Default list of scopes.
     */
    public function scopes()
    {
        return $this->scaffoldScopes();
    }

    protected function scaffoldFilters()
    {
        $this->filters = new Mutable();

        if ($model = $this->model()) {
            $columns = table_columns($model);
            $indexes = table_indexes($model);

            foreach ($indexes as $column) {
                $data = $columns[$column];

                switch (class_basename($data->getType())) {
                    case 'StringType':
                        if (connection('mysql') && null === $data->getLength()) {
                            if ($values = enum_values($model->getTable(), $column)) {
                                $filter = $this->filterFactory($column, 'select');
                                $filter->getInput()->setOptions(['' => '--Any--'] + $values);

                                $this->addFilter($filter);

                                break;
                            }
                        }

                        $this->addFilter(
                            $this->filterFactory($column, 'text')
                        );

                        break;
                    case 'DateTimeType':
                        $this->addFilter(
                            $this->filterFactory($column, 'daterange')
                        );

                        break;
                    case 'BooleanType':
                        $this->addFilter(
                            $this->filterFactory(
                                $column,
                                'select',
                                '',
                                [
                                    '' => '--Any--',
                                    1 => 'Yes',
                                    0 => 'No',
                                ]
                            )
                        );

                        break;
                }
            }
        }

        return $this->filters;
    }

    /**
     * Find all public scopes in current model.
     *
     * @return Mutable
     */
    protected function scaffoldScopes()
    {
        $this->scopes = new Mutable();

        if ($model = $this->model()) {
            $this->fetchModelScopes($model);

            $this->addSoftDeletesScopes($model);
        }

        return $this->scopes;
    }

    protected function filterFactory($name, $type = 'text', $label = '', array $options = [], callable $query = null)
    {
        $element = FilterElement::$type($name);

        $input = FilterInputFactory::make($name, $type);

        if (null !== $query) {
            $input->setQuery($query);
        }

        if ('select' === $type && is_array($options)) {
            $input->setOptions($options);
        }

        return $element->setInput($input);
    }

    /**
     * Parse the model for scopes.
     *
     * @param $model
     */
    protected function fetchModelScopes($model)
    {
        $reflection = new \ReflectionClass($model);

        foreach ($reflection->getMethods() as $method) {
            if (preg_match('~^scope(.+)$~i', $method->name, $match)) {
                if ($this->isHiddenScope($name = $match[1])
                    || $this->isDynamicScope($method)
                    || $this->hasHiddenFlag($method->getDocComment())
                    || 'scopeTranslated' === $match[0]) {
                    continue;
                }

                $scope = with(new Scope($name))->setQuery([$model, $name]);
                if ($icon = $this->hasIconFlag($method->getDocComment())) {
                    $scope->setIcon($icon);
                }

                $this->addScope($scope);
            }
        }
    }

    /**
     * @param $method
     *
     * @return int
     */
    protected function isDynamicScope($method)
    {
        return count($method->getParameters()) > 1;
    }

    /**
     * Exists in user-defined hiddenScopes property.
     *
     * @param $name
     *
     * @return bool
     */
    protected function isHiddenScope($name)
    {
        return property_exists($this, 'hiddenScopes') && in_array($name, $this->hiddenScopes, true);
    }

    /**
     * Marked with @hidden flag.
     *
     * @param $docBlock
     *
     * @return int
     */
    protected function hasHiddenFlag($docBlock)
    {
        return preg_match('~\@hidden~si', $docBlock);
    }

    /**
     * Marked with @hidden flag.
     *
     * @param $docBlock
     *
     * @return int
     */
    protected function hasIconFlag($docBlock)
    {
        preg_match('~\@icon\s+([^\\s]+)~is', $docBlock, $icon);

        return $icon ? $icon[1] : null;
    }

    /**
     * Add SoftDeletes scopes if model uses that trait.
     *
     * @param $model
     */
    protected function addSoftDeletesScopes($model)
    {
        if (array_key_exists('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model))) {
            foreach ($this->softDeletesScopes() as $method => $name) {
                $this->addScope(
                    with(new Scope($method))
                        ->setTitle($name)
                        ->setQuery([$model, $method])
                );
            }
        }
    }

    /**
     * @return array
     */
    protected function softDeletesScopes()
    {
        return ['onlyTrashed' => 'Only Trashed', 'withTrashed' => 'With Trashed'];
    }
}
