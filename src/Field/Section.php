<?php

namespace Terranet\Administrator\Field;

use Terranet\Administrator\Scaffolding;

class Section extends Id
{
    /** @var array */
    protected $visibility = [
        Scaffolding::PAGE_INDEX => false,
        Scaffolding::PAGE_EDIT => true,
        Scaffolding::PAGE_VIEW => true,
    ];
}
