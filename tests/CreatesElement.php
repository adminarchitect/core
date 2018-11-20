<?php

namespace Terranet\Administrator\Tests;

use Terranet\Administrator\Columns\Element;
use Terranet\Administrator\Field\Text;

trait CreatesElement
{
    /**
     * Make an element if not exists.
     *
     * @param $name
     *
     * @return mixed
     */
    protected function e($name)
    {
        static $elements = [];

        if (!array_key_exists($name, $elements)) {
            $elements[$name] = Text::make($name);
        }

        return $elements[$name];
    }
}
