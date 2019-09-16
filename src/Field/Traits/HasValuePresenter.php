<?php

namespace Terranet\Administrator\Field\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Terranet\Presentable\PresentableInterface;

trait HasValuePresenter
{
    /**
     * Check if a model has field's presenter.
     *
     * @param Model $model
     * @param string $field
     *
     * @return null|callable
     */
    public function hasPresenter(Model $model, string $field): ?callable
    {
        return $model instanceof PresentableInterface
        && method_exists($model->present(), $method = $this->presenterMethod($field))
            ? [$model->present(), $method]
            : null;
    }

    /**
     * @param callable $presenter
     *
     * @return mixed
     */
    public function callPresenter(callable $presenter)
    {
        return \call_user_func_array($presenter, [$this->value()]);
    }

    /**
     * @param string $fieldName
     *
     * @return string
     */
    public function presenterMethod(string $fieldName): string
    {
        return 'admin'.Str::title(Str::camel($fieldName));
    }
}
