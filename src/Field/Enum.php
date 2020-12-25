<?php

namespace Terranet\Administrator\Field;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Terranet\Administrator\Exception;
use Terranet\Administrator\Traits\Form\SupportsListTypes;

class Enum extends Field
{
    use SupportsListTypes;

    /** @var array */
    public $options = [];

    /** @var array */
    public $colors = ['#777777', '#2574ab', '#259dab', '#5bc0de', '#e6ad5c', '#d9534f'];

    /** @var array */
    public $palette = [];

    /** @var bool */
    public $useColors = true;

    /**
     * @return $this
     */
    public function disableColors(): self
    {
        $this->useColors = false;

        return $this;
    }

    /**
     * @param iterable $options
     * @return self
     */
    public function setOptions(iterable $options): self
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $i = 0;
        foreach ($options as $key => $value) {
            if (!Arr::has($this->palette, $key)) {
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
     * @param mixed $color
     * @param null|string $value
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
    protected function onIndex(): array
    {
        return $this->onEdit();
    }

    /**
     * @return array
     */
    protected function onEdit(): array
    {
        $color = $this->useColors && $this->value() ? Arr::get($this->palette, $this->value()) : null;

        return compact('color');
    }

    /**
     * @return array
     */
    protected function onView(): array
    {
        return $this->onEdit();
    }
}
