<?php

namespace Terranet\Administrator\Field\Traits;

trait AcceptsCustomFormat
{
    /** @var null\Closure */
    protected $format;

    /**
     * @param \Closure $format
     * @return BelongsTo
     */
    public function setCustomFormat(\Closure $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @param $relation
     * @return mixed
     */
    public function callFormatter($relation)
    {
        return call_user_func_array($this->format, [$relation, $this->model]);
    }
}