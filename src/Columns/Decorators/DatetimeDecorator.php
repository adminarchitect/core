<?php

namespace Terranet\Administrator\Columns\Decorators;

use Carbon\Carbon;

class DatetimeDecorator extends CellDecorator
{
    protected $type;

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getDecorator()
    {
        return function ($row) {
            $value = \admin\helpers\eloquent_attribute($row, $this->name);

            if ($value instanceof Carbon
                && method_exists($value, $method = "to" . str_replace('Type', '', $this->type) . "String")
            ) {
                return '<span class="label label-primary">' . $value->$method() . '</span>';
            }

            return $value;
        };
    }
}
