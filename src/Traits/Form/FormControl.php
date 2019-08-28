<?php

namespace Terranet\Administrator\Traits\Form;

trait FormControl
{
    /**
     * Element name.
     *
     * @var string
     */
    protected $name;

    /**
     * Element Value.
     *
     * @var null
     */
    protected $value;

    /**
     * HTML attributes.
     *
     * @var array
     */
    protected $attributes = [
        'class' => 'form-control',
    ];

    /**
     * Options: used for Select element.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Get element name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function getFormName()
    {
        if ($this->relation) {
            return $this->translatable
                ? "{$this->relation}[translatable]".substr($this->name, strlen('translatable'))
                : "{$this->relation}[$this->name]";
        }

        return $this->name;
    }

    /**
     * Set element name.
     *
     * @param $name
     *
     * @return mixed
     */
    public function setName($name)
    {
        if (false !== stripos($name, '.')) {
            $relations = explode('.', $name);
            $name = array_pop($relations);
            $this->relation = implode('.', $relations);
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Get element value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set element implicit value.
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value = null)
    {
        if ($value instanceof \Closure) {
            $value = call_user_func($value);
        }

        // In tests, scaffold.model is not bound, and handleJsonType() raises error.
        if (app()->bound('scaffold.model')) {
            $value = $this->handleJsonType($value);
        }

        $this->value = $value;

        return $this;
    }

    /**
     * Get elemtn type.
     *
     * @return string
     */
    public function getType()
    {
        $parts = explode('\\', get_class($this));

        return strtolower(array_pop($parts));
    }

    /**
     * Set element attributes.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;

        $this->decoupleOptionsFromAttributes();

        return $this;
    }

    /**
     * @return bool
     */
    protected function hasValue()
    {
        return null !== $this->value && !is_callable($this->value);
    }

    protected function decoupleOptionsFromAttributes()
    {
        foreach ($this->attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $method = 'set'.ucfirst($key);

                method_exists($this, $method) ? $this->$method($value) : ($this->$key = $value);

                unset($this->attributes[$key]);
            }
        }
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function handleJsonType($value)
    {
        if (($repo = $this->getRepository())) {
            if (($cast = array_get($repo->getCasts(), $this->name)) && in_array($cast, ['array', 'json'], true)) {
                $value = json_encode($value);
            }
        }

        return $value;
    }
}
