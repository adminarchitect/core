<?php

namespace Terranet\Administrator\Field;

class Phone extends Field
{
    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return ['style' => 'width: 250px'] + parent::getAttributes();
    }
}
