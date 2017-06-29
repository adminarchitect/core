<?php

namespace Terranet\Administrator\Filters\Element;

use Terranet\Administrator\Contracts\Form\Queryable;
use Terranet\Administrator\Form\Type\Text as FormText;
use Terranet\Administrator\Traits\Form\ExecutesQuery;

class Text extends FormText implements Queryable
{
    use ExecutesQuery;

    protected $attributes = [
        'class' => 'form-control'
    ];
}
