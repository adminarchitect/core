<?php

namespace Terranet\Administrator\Form;

use Terranet\Administrator\Exception;

class InputFactory
{
    /**
     * Make input element of type $type.
     *
     * @param $name
     * @param string $type
     * @return mixed
     * @throws Exception
     */
    public static function make($name, $type = 'text')
    {
        $input = "Terranet\\Administrator\\Form\\Type\\" . ucfirst($type);

        if (!class_exists($input)) {
            throw new Exception(sprintf("Unknown type: %s", $type));
        }

        return new $input($name);
    }
}