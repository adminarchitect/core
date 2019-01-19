<?php

namespace Terranet\Administrator\Field\Traits;

trait HandlesVisibility
{
    /** @var null|\Closure */
    protected $when;

    /**
     * @return bool
     */
    public function visibleWhen(): bool
    {
        return $this->when ? $this->when->call($this, request()) : true;
    }
}
