<?php

namespace Terranet\Administrator\Field;

use Illuminate\Contracts\Support\Arrayable;
use Terranet\Administrator\Exception;

class Enum extends Field
{
    /** @var array */
    protected $options = [];

    /** @var array */
    protected $colors = ['#777777', '#2574ab', '#259dab', '#5bc0de', '#e6ad5c', '#d9534f'];

    /** @var array */
    protected $palette = [];

    /** @var bool */
    protected $useColors = true;

    /**
     * @return $this
     */
    public function disableColors()
    {
        $this->useColors = false;

        return $this;
    }

    /**
     * @param  iterable  $options
     * @return self
     */
    public function setOptions(iterable $options): self
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $i = 0;
        foreach ($options as $key => $value) {
            if (!array_has($this->palette, $key)) {
                $this->palette[$key] = $this->colors[$i % \count($this->colors)];
                ++$i;
            }
        }
        $this->options = $options;

        return $this;
    }

    /**
     * Set colors palette.
     *
     * @param  mixed  $color
     * @param  null|string  $value
     * @return Enum
     * @throws Exception
     */
    public function palette($color, string $value = null)
    {
        if (\is_array($color)) {
            foreach ($color as $name => $code) {
                $this->palette($name, $code);
            }

            return $this;
        }

        if (!array_key_exists($color, $this->options)) {
            throw new Exception("Unknown option {$color}");
        }

        $this->palette[$color] = $value;

        return $this;
    }

    /**
     * @return array
     */
    protected function onEdit()
    {
        return [
            'options' => $this->options ?: [],
            'color' => $this->useColors ? \Illuminate\Support\Arr::get($this->palette, $this->value()) : null,
        ];
    }

    /**
     * @return array
     */
    protected function onIndex()
    {
        return $this->onEdit();
    }

    /**
     * @return array
     */
    protected function onView()
    {
        return $this->onEdit();
    }
}
