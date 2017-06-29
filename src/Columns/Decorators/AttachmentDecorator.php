<?php

namespace Terranet\Administrator\Columns\Decorators;

class AttachmentDecorator extends CellDecorator
{
    protected $style = 'original';

    protected $rounded = false;

    protected $attributes = [
        'width' => 75,
        'height' => 75,
    ];

    public function getDecorator()
    {
        return function ($row) {
            return \admin\output\staplerImage($row->{$this->name}, $this->style, $this->attributes());
        };
    }

    public function setStyle($style)
    {
        $this->style = $style;

        return $this;
    }

    public function setSize($width, $height)
    {
        $this->attributes = array_merge($this->attributes(), [
            'width' => $width,
            'height' => $height,
        ]);
    }

    /**
     * @param bool $rounded
     * @return $this
     */
    public function setRounded(bool $rounded)
    {
        $this->rounded = $rounded;

        return $this;
    }

    protected function attributes()
    {
        if (!array_key_exists('id', $this->attributes)) {
            $this->attributes['id'] = $this->name;
        }

        $this->attributes['class'] = "img-responsive";

        if ($this->rounded) {
            $this->attributes['class'] = "img-circle";
        }

        return $this->attributes;
    }
}
