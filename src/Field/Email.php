<?php

namespace Terranet\Administrator\Field;

class Email extends Field
{
    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return ['style' => 'width: 250px'] + parent::getAttributes();
    }
}
