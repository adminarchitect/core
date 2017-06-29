<?php

namespace Terranet\Administrator\Filters\Element;

use Terranet\Administrator\Contracts\Form\Queryable;
use Terranet\Administrator\Form\Type\Date as FormDate;
use Terranet\Administrator\Traits\Form\ExecutesQuery;

class Date extends FormDate implements Queryable
{
    use ExecutesQuery;
}
