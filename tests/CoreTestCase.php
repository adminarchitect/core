<?php

namespace Terranet\Administrator\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CoreTestCase extends TestCase
{
    /**
     * Call protected/private method of a class.
     *
     * @param mixed  &$object Instantiated object that we will run method on
     * @param string $method Method name to call
     * @param array  $args array of parameters to pass into method
     *
     * @throws \ReflectionException
     *
     * @return mixed method return
     */
    public function invokeMethod(&$object, $method, array $args = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }
}
