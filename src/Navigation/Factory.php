<?php

namespace Terranet\Administrator\Navigation;

use App\Http\Terranet\Administrator\Navigation;

class Factory
{
    public function make()
    {
        return ($navigation = app('menus'))
            ? (new Navigation($navigation))->make($navigation)
            : null;
    }
}
