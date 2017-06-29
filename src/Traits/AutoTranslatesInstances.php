<?php

namespace Terranet\Administrator\Traits;

trait AutoTranslatesInstances
{
    protected $translator;

    public function translator()
    {
        return $this->translator ?: app('translator');
    }

    public function setTranslator($translator)
    {
        $this->translator = $translator;

        return $this;
    }
}