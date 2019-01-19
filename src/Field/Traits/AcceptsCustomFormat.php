<?php

namespace Terranet\Administrator\Field\Traits;

use Closure;

trait AcceptsCustomFormat
{
    /** @var null|Closure */
    protected $format;

    /**
     * @return bool
     */
    public function hasCustomFormat(): bool
    {
        return null !== $this->format;
    }

    /**
     * @param Closure $format
     *
     * @return self
     */
    public function renderAs(Closure $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @param $args
     *
     * @return mixed
     */
    protected function callFormatter(...$args)
    {
        return $this->format->call($this, ...$args);
    }
}
