<?php

namespace Terranet\Administrator\Traits;

trait Stringify
{
    public function __toString()
    {
        return (string) $this->render();
    }
}
