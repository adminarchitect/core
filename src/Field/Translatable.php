<?php

namespace Terranet\Administrator\Field;

use Terranet\Administrator\Exception;
use Terranet\Administrator\Traits\Form\RendersTranslatableElement;

class Translatable
{
    use RendersTranslatableElement;

    protected $field;

    /**
     * Translatable constructor.
     *
     * @param Generic $field
     */
    public function __construct(Generic $field)
    {
        $this->field = $field;
    }

    /**
     * Proxy field methods calls.
     *
     * @param $method
     * @param $args
     */
    public function __call($method, $args)
    {
        if (method_exists($this->field, $method)) {
            return call_user_func_array([$this->field, $method], $args);
        }

        throw new Exception(sprintf('Unknown method [%s]', $method));
    }
}