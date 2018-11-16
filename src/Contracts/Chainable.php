<?php

namespace Terranet\Administrator\Contracts;

interface Chainable
{
    /**
     * @param Chainable $instance
     * @return mixed
     */
    public function setNext(Chainable $instance);
}