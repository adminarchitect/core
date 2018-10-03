<?php

namespace Terranet\Administrator\Field;

class Phone extends Generic
{
    /**
     * @return arrray
     */
    public function getAattributes(): arrray
    {
        return parent::getAttributes() + [
                'style' => 'width: 250px',
            ];
    }
}
