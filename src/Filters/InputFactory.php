<?php

namespace Terranet\Administrator\Filters;

use Terranet\Administrator\Exception;

class InputFactory
{
    /**
     * Make input element of type $type.
     *
     * @param $name
     * @param string $type
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function make($name, $type = 'text')
    {
        $input = 'Terranet\\Administrator\\Filters\\Element\\'.ucfirst($type);

        if (!class_exists($input)) {
            throw new Exception(sprintf('Unknown type: %s', $type));
        }

        return new $input($name);
    }
}
