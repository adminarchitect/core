<?php

namespace Terranet\Administrator\Columns\Decorators;

class TextDecorator extends CellDecorator
{
    protected $limit = 200;

    protected $end = '...';

    public function setLimit($limit)
    {
        $this->limit = (int) $limit;

        return $this;
    }

    public function setEnd($string)
    {
        $this->end = (int) $string;

        return $this;
    }

    protected function render($row)
    {
        return
            '<span class="text-muted">'.
            str_limit(
                strip_tags(\admin\helpers\eloquent_attribute($row, $this->name)),
                $this->limit,
                $this->end
            )
            .'</span>';
    }
}
