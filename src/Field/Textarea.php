<?php

namespace Terranet\Administrator\Field;

class Textarea extends Generic
{
    /** @var array */
    protected $visibility = [
        self::PAGE_INDEX => false,
        self::PAGE_EDIT => true,
        self::PAGE_VIEW => true,
    ];
}