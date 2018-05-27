<?php

namespace Terranet\Administrator\Traits;

use ReflectionClass;
use ReflectionMethod;

trait MethodsCollector
{
    /**
     * @param $instance
     * @param int $filter
     *
     * @return \ReflectionMethod[]
     */
    protected function collectMethods($instance, $filter = ReflectionMethod::IS_PUBLIC)
    {
        static $methodsCache = null;

        if (null === $methodsCache) {
            $methodsCache = $instance
                ? (new ReflectionClass($instance))->getMethods($filter)
                : [];
        }

        return $methodsCache;
    }
}
