<?php

namespace Terranet\Administrator\Form\Type;

use Form;
use Terranet\Administrator\Form\Element;
use Terranet\Administrator\Traits\CallableTrait;

class Select extends Element
{
    use CallableTrait;

    /**
     * List of options.
     *
     * @var array
     */
    protected $options = [];

    protected $attributes = [
        'class' => 'form-control',
    ];

    protected $rules = [
    ];

    public function setOptions($options)
    {
        /*
         * Multiple Options can be provided using different styles
         * 1. closure function() { return ['list', 'of', 'values']; }
         * 2. callable [$object, "method"]
         * 3. string "Class@Method"
         */
        if ($callable = $this->callableOptions($options)) {
            $options = call_user_func($callable);
        }

        $this->options = $options;

        return $this;
    }

    public function getOptions()
    {
        $options = $this->options;

        if (empty($options)) {
            return [];
        }

        if (is_callable($options)) {
            return $this->callback($options);
        }

        return (array) $options;
    }

    public function setMultiple($flag = true)
    {
        $this->attributes['multiple'] = (bool) $flag;

        return $this;
    }

    public function render()
    {
        $name = $this->getFormName();

        if (isset($this->attributes['multiple']) && $this->attributes['multiple']) {
            $this->attributes['id'] = Form::getIdAttribute($name, $this->attributes);
        }

        return Form::select($name, $this->options, $this->value, $this->attributes);
    }

    public function getFormName()
    {
        $name = parent::getFormName();

        if (isset($this->attributes['multiple']) && $this->attributes['multiple']) {
            $name = $name.'[]';
        }

        return $name;
    }

    /**
     * Parse options and try to resolve values.
     *
     * @param $callable
     *
     * @return mixed bool|array|callable
     */
    protected function callableOptions($callable)
    {
        // resolve closure
        if (is_callable($callable)) {
            return $callable;
        }

        // resolve callable "Class@method" style
        if (is_string($callable) && list($class, $method) = explode('@', $callable)) {
            return [app()->make($class), $method];
        }

        // resolve callable [$object, "method"]
        if (2 === count($callable) && array_key_exists(0, $callable) && array_key_exists(1, $callable)) {
            if (is_object($class = $callable[0]) && is_string($method = $callable[1]) && method_exists($class, $method)) {
                return [$class, $method];
            }
        }

        return false;
    }
}
