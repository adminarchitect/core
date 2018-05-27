<?php

namespace Terranet\Administrator\Traits;

trait CallableTrait
{
    public function callback()
    {
        $arguments = func_get_args();
        $callback = array_shift($arguments);

        $reflection = new \ReflectionFunction($callback);
        $parameters = $reflection->getParameters();

        foreach ($parameters as $parameter) {
            $className = $parameter->getClass();

            if ($className && $name = $className->getName()) {
                array_unshift($arguments, \App::make($name));
            }
        }

        return call_user_func_array($callback, $arguments);
    }
}
