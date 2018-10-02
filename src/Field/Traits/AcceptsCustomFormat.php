<?php

namespace Terranet\Administrator\Field\Traits;

trait AcceptsCustomFormat
{
    /** @var null\Closure */
    protected $format;

    /**
     * @return bool
     */
    public function hasFormat()
    {
        return null !== $this->format;
    }

    /**
     * @param \Closure $format
     *
     * @return BelongsTo
     */
    public function renderAs(\Closure $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @param $args
     *
     * @return mixed
     */
    protected function callFormatter($args)
    {
        return call_user_func_array($this->format, $args);
    }
}
