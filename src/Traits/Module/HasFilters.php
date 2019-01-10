<?php

namespace Terranet\Administrator\Traits\Module;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Terranet\Administrator\Annotations\ScopeFilter;
use Terranet\Administrator\Collection\Mutable;
use Terranet\Administrator\Filter\Enum;
use Terranet\Administrator\Filter\Filter;
use Terranet\Administrator\Filter\Text;
use Terranet\Administrator\Filters\FilterElement;
use Terranet\Administrator\Filters\InputFactory as FilterInputFactory;
use Terranet\Administrator\Filters\Scope;
use function admin\db\connection;
use function admin\db\enum_values;
use function admin\db\table_columns;
use function admin\db\table_indexes;
use function admin\db\translated_values;

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
     * @param Filter $filter
     *
     * @return $this
     */
    public function addFilter(Filter $filter)
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

    /**
     * @return Mutable
     */
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
                        if (connection('mysql') && !$data->getLength()) {
                            if ($values = enum_values($model->getTable(), $column)) {
                                $values = translated_values($values, $this->url(), $column);

                                $this->addFilter(
                                    Enum::make($column, $column)->setOptions(['' => '----'] + $values)
                                );

                                break;
                            }
                        }

                        $this->addFilter(
                            Text::make($column, $column)
                        );

                        break;
                    case 'DateTimeType':
                        $this->addFilter(
                            DateRange::make($column, $column)
                        );

                        break;
                    case 'BooleanType':
                        $this->addFilter(
                            Enum::make($column, $column)->setOptions(
                                [
                                    '' => trans('administrator::buttons.any'),
                                    1 => trans('administrator::buttons.yes'),
                                    0 => trans('administrator::buttons.no'),
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

    /**
     * Parse the model for scopes.
     *
     * @param $model
     */
    protected function fetchModelScopes($model)
    {
        $reflection = new \ReflectionClass($model);

        foreach ($reflection->getMethods() as $method) {
            /** @var ScopeFilter $info */
            if ($info = app('scaffold.annotations')->getMethodAnnotation($method, ScopeFilter::class)) {
                if ($this->isDynamicScope($method) || 'scopeTranslated' === $method->getName()) {
                    continue;
                }

                $name = $callback = str_replace('scope', '', $method->getName());

                if ($info->name || $info->translate) {
                    $name = $info->name ?: trans($info->translate);
                };

                $scope = new Scope($name, str_slug($callback, '_'));
                $scope->setQuery([$model, $callback]);
                if ($info->icon) {
                    $scope->setIcon($info->icon);
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
        return \count($method->getParameters()) > 1;
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
                $scope = new Scope($name, $method);
                $scope->setQuery([$model, $method]);

                $this->addScope($scope);
            }
        }
    }

    /**
     * @return array
     */
    protected function softDeletesScopes()
    {
        return [
            'onlyTrashed' => trans('administrator::buttons.only_trashed'),
            'withTrashed' => trans('administrator::buttons.with_trashed'),
        ];
    }
}
