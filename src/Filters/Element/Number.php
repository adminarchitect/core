<?php

namespace Terranet\Administrator\Filters\Element;

use Terranet\Administrator\Contracts\Form\Queryable;
use Terranet\Administrator\Form\Type\Number as FormNumber;
use Terranet\Administrator\Traits\Form\ExecutesQuery;

class Number extends FormNumber implements Queryable
{
    use ExecutesQuery;
}
