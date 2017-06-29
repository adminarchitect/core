<?php

namespace Terranet\Administrator\Filters\Element;

use Terranet\Administrator\Contracts\Form\Queryable;
use Terranet\Administrator\Form\Type\Datalist as FormDatalist;
use Terranet\Administrator\Traits\Form\ExecutesQuery;

class Datalist extends FormDatalist implements Queryable
{
    use ExecutesQuery;
}
