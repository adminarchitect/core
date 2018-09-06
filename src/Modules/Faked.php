<?php

namespace Terranet\Administrator\Modules;

use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Scaffolding;

/**
 * Class Faked
 * @package Terranet\Administrator\Modules
 */
class Faked extends Scaffolding implements Module
{
    /**
     * @param $model
     * @return Faked
     */
    public static function make($model)
    {
        $module = new static();
        $module->setModel($model);

        return $module;
    }
}