<?php

namespace Terranet\Administrator\Columns\Decorators;

use Terranet\Presentable\PresentableInterface;
use function admin\helpers\has_admin_presenter;
use function admin\helpers\has_presenter;
use function admin\helpers\present;

abstract class CellDecorator
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getDecorator()
    {
        return function ($row) {
            return $this->presentable($row)
                ? $this->present($row, $this->name)
                : $this->render($row);
        };
    }

    protected function presentable($row)
    {
        return ($row instanceof PresentableInterface)
            && (has_admin_presenter($row, $this->name) || has_presenter($row, $this->name));
    }

    protected function present($row, $key)
    {
        return present($row, $key);
    }

    abstract protected function render($row);
}
