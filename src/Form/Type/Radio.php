<?php

namespace Terranet\Administrator\Form\Type;

use Form;

class Radio extends Select
{
    protected $attributes = [];

    protected $style = [
        'display' => 'inline-block',
        'margin-right' => '15px',
    ];

    public function render()
    {
        $name = $this->getFormName();
        $id = trim(preg_replace('~[^a-z]+~si', '_', $name), '_');

        $out = [];
        foreach ($this->getOptions() as $value => $label) {
            $attributes = array_merge((array) $this->attributes, ['id' => $partId = $this->particularId($id, $value)]);

            array_push(
                $out,
                '<div style="' . $this->getStyle(true) . '">' .
                '   ' . $this->htmlInput($name, $value, $attributes) .
                '   ' . PHP_EOL . '&nbsp;' .
                '   ' . Form::label($partId, $label) .
                '</div>'
            );
        }

        return implode(PHP_EOL, $out);
    }

    /**
     * Apply a style to a single element.
     *
     * @param array $style
     * @param bool $update
     * @return $this
     */
    public function setStyle(array $style = [], $update = true)
    {
        $this->style = $update
            ? array_merge($this->style, $style)
            : $style;

        return $this;
    }

    /**
     * @param bool $compiled
     * @return array
     */
    public function getStyle($compiled = false)
    {
        if ($compiled) {
            $style = [];
            foreach ($this->style as $property => $value) {
                $style[] = "{$property}: {$value}";
            }

            return implode("; ", $style);
        }

        return $this->style;
    }

    /**
     * @param $name
     * @param $value
     * @param $attributes
     * @return mixed
     */
    protected function htmlInput($name, $value, $attributes)
    {
        return Form::radio($name, $value, in_array($value, (array) $this->value), $attributes);
    }

    /**
     * @return array
     */
    protected function hiddenAttributes()
    {
        return ['id' => Form::getIdAttribute($this->getFormName(), $this->attributes) . '_hidden'];
    }

    /**
     * @param $id
     * @param $value
     * @return string
     */
    protected function particularId($id, $value)
    {
        return $id . '_' . $value;
    }
}