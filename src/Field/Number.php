<?php

namespace Terranet\Administrator\Field;

class Number extends Field
{
    public function getAttributes(): array
    {
        return ['style' => 'width: 150px'] + parent::getAttributes();
    }
}
