<?php

namespace Terranet\Administrator\Form\Type;

use Form;
use Illuminate\Support\HtmlString;

class Datalist extends Select
{
    protected $rules = [
    ];

    public function render()
    {
        $name = $this->getName();

        $id = 'scaffold_'.str_slug($name);
        $attributes = array_merge($this->attributes, [
            'list' => $id,
        ]);

        $out[] = Form::text($this->getFormName(), $this->getValue(), $attributes);

        $out[] = '<datalist id="'.$id.'">';
        foreach ($this->getOptions() as $key => $option) {
            if (is_numeric($key)) {
                $out[] = '<option value="'.$option.'"></option>';
            } else {
                $out[] = '<option value="'.$key.'">'.$option.'</option>';
            }
        }
        $out[] = '</datalist>';

        return new HtmlString(implode(PHP_EOL, $out));
    }
}
