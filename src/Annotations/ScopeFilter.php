<?php

namespace Terranet\Administrator\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
final class ScopeFilter
{
    /** @var string */
    public $name;

    /** @var string */
    public $translate;

    /** @var string */
    public $icon;
}
