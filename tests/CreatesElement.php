<?php

use Terranet\Administrator\Columns\Element;

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
            $elements[$name] = new Element($name);
        }

        return $elements[$name];
    }
}
