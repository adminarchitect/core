<?php

namespace Terranet\Administrator\Traits\Form;

use Illuminate\Contracts\Validation\Factory;
use Terranet\Administrator\Exceptions\WrongFieldAttributeException;

trait ValidatesFormElement
{
    /**
     * Element's Validation Rules.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Validation errors.
     *
     * @var array
     */
    protected $errors = [];

    static protected $validator = null;

    /**
     * @param array $rules
     *
     * @return $this
     */
    public function setRules(array $rules = [])
    {
        $this->rules = $rules;

        return $this;
    }

    protected function validateAttributes()
    {
        $validator = $this->getValidator();

        if ($validator->fails()) {
            $message = sprintf(
                "Field \"{$this->name}\" fails with messages: %s",
                join("; ", $validator->getMessageBag()->all())
            );
            throw new WrongFieldAttributeException($message);
        }
    }

    /**
     * Check if
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function errors()
    {
        return view('administrator::partials.forms.errors')->with('errors', $this->errors ?: []);
    }

    /**
     * @return mixed
     */
    public function getValidator()
    {
        if (null === static::$validator) {
            static::$validator = app(Factory::class)->make($this->attributes, $this->rules);
        }

        return static::$validator;
    }

    static public function setValidator($validator)
    {
        static::$validator = $validator;
    }
}
