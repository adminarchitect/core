<?php

namespace Terranet\Administrator\Filters\Element;

use Terranet\Administrator\Contracts\Form\Queryable;
use Terranet\Administrator\Form\Type\Daterange as FormDaterange;
use Terranet\Administrator\Traits\Form\ExecutesQuery;

class Daterange extends FormDaterange implements Queryable
{
    use ExecutesQuery;
}
