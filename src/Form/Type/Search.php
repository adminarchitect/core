<?php

namespace Terranet\Administrator\Form\Type;

use Form;

class Search extends Select
{
    /**
     * The specific defaults for subclasses to override.
     *
     * @var array
     */
    protected $attributes = [
        'data-type' => 'livesearch',
        'data-url' => null,
        'class' => 'form-control',
    ];

    public function setDataUrl($url)
    {
        $this->attributes['data-url'] = $url;

        return $this;
    }

    public function getDataUrl()
    {
        return array_get($this->attributes, 'data-url', null);
    }
}
