<?php

namespace Terranet\Administrator\Filters\Element;

use Terranet\Administrator\Contracts\Form\Queryable;
use Terranet\Administrator\Form\Type\Select as FormSelect;
use Terranet\Administrator\Traits\Form\ExecutesQuery;

class Select extends FormSelect implements Queryable
{
    use ExecutesQuery;
}
