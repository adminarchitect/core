<?php

namespace Terranet\Administrator\Form\Type;

use Terranet\Administrator\Form\Element;

class View extends Element
{
    protected $rules = [
        'view' => 'required'
    ];

    /**
     * Each subclass should have this method realized.
     *
     * @return mixed
     */
    public function render()
    {
        return view($this->getView())->with($this->getViewParams());
    }
}