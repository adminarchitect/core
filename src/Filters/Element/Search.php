<?php

namespace Terranet\Administrator\Filters\Element;

use Terranet\Administrator\Contracts\Form\Queryable;
use Terranet\Administrator\Form\Type\Search as FormSearch;
use Terranet\Administrator\Traits\Form\ExecutesQuery;

class Search extends FormSearch implements Queryable
{
    use ExecutesQuery;

    protected $attributes = [
        'data-type' => 'livesearch',
        'data-url' => null,
        'class' => 'form-control',
        'placeholder' => 'Search...'
    ];
}
