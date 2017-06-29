<?php

namespace Terranet\Administrator\Navigation\Presenters\Bootstrap;

use Pingpong\Menus\Presenters\Bootstrap\NavbarPresenter as CorePresenter;

class NavbarPresenter extends CorePresenter
{
    public function getOpenTagWrapper()
    {
        return PHP_EOL . '<ul class="dropdown-menu pull-right">' . PHP_EOL;
    }
}
