<?php

namespace Terranet\Administrator\Form;

use Terranet\Administrator\Contracts\Form\Element as BasicFormElement;
use Terranet\Administrator\Exception;
use Terranet\Administrator\Form\Type\Text;
use Terranet\Administrator\Form\Type\View;
use Terranet\Administrator\Traits\Collection\ElementContainer;

/**
 * Since Form builder is based on a Mutable collection
 * and each element of Mutable collection should be
 * instance of a special type, each form element
 * is just injected to this object-container.
 *
 * @package Terranet\Administrator\Form
 *
 * @method static FormElement text(string $name)
 * @method static FormElement view(string $name)
 * @method static FormElement search(string $name)
 * @method static FormElement textarea(string $name)
 * @method static FormElement medium(string $name)
 * @method static FormElement tinymce(string $name)
 * @method static FormElement ckeditor(string $name)
 * @method static FormElement boolean(string $name)
 * @method static FormElement radio(string $name, array $attributes, array $options)
 * @method static FormElement multiCheckbox(string $name, array $attributes = [], array $options = [])
 * @method static FormElement datalist(string $name, array $attributes = [], array $options = [])
 * @method static FormElement date(string $name)
 * @method static FormElement daterange(string $name)
 * @method static FormElement datetime(string $name)
 * @method static FormElement time(string $name)
 * @method static FormElement email(string $name)
 * @method static FormElement file(string $name)
 * @method static FormElement hidden(string $name)
 * @method static FormElement image(string $name)
 * @method static FormElement key(string $name)
 * @method static FormElement markdown(string $name)
 * @method static FormElement number(string $name)
 * @method static FormElement password(string $name)
 * @method static FormElement select(string $name, array $attributes, array $options)
 * @method static FormElement tel(string $name)
 */
class FormElement extends ElementContainer
{
    /**
     * Get wrapped input object.
     *
     * @var BasicFormElement
     */
    protected $input;

    /**
     * BasicFormElement description.
     *
     * @var
     */
    protected $description;

    public function __construct($id)
    {
        parent::__construct($id);

        $this->setInput(
            $this->defaultInputType($id)
        );

        if ($this->translator()->has($key = $this->descriptionKey())) {
            $this->setDescription(trans($key));
        }
    }

    /**
     * Init FormElement object by calling static method.
     *
     * @example: FormElement::text('title')
     * @param $inputType
     * @param $arguments
     * @note:
     *  1st argument = name,
     *  2nd argument = html options,
     *  3rd argument = values (select, radio, multicheckbox)
     * @return
     */
    public static function __callStatic($inputType, $arguments)
    {
        $inputType = InputFactory::make(
            $name = $arguments[0],
            $inputType
        );

        # View element receives 2nd optional argument -> path to view script
        if (is_a($inputType, View::class) && ($view = array_get($arguments, 1))) {
            $inputType->setView($view);
        }

        $element = (new static($name))->setInput($inputType);

        $input = $element->getInput();

        if (is_array($attributes = array_get($arguments, 1))) {
            $input->setAttributes($attributes);
        }

        if (is_array($options = array_get($arguments, 2)) && !empty($options)
            && method_exists($input, "setOptions")
        ) {
            $input->setOptions($options);
        }

        return $element;
    }

    public function __call($method, $arguments)
    {
        if (method_exists($input = $this->getInput(), $method)) {
            call_user_func_array([$input, $method], $arguments);

            return $this;
        }

        throw new \Exception("Call to undefined method: " . class_basename($this) . "::$method");
    }

    /**
     * Create a custom Form element.
     *
     * @param $id
     * @param mixed string|BasicFormElement $input
     * @return mixed
     */
    public static function custom($id, $input)
    {
        return (new static($id))->setInput($input instanceof BasicFormElement ? $input : new $input($id));
    }

    /**
     * @return mixed
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Set wrapped input object.
     *
     * @param mixed string|FormElement $input
     * @return $this
     * @throws Exception
     */
    public function setInput($input)
    {
        if (!(is_string($input) || $input instanceof BasicFormElement)) {
            throw new Exception("Input must be a String or instance of FormElement Contract");
        }

        if (is_string($input)) {
            $input = $this->makeInput($input);
        }

        $this->input = $input;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param $id
     * @return Text
     */
    protected function defaultInputType($id)
    {
        return new Text($id);
    }

    /**
     * Make a Filter element.
     *
     * @param $input
     * @return mixed
     */
    protected function makeInput($input)
    {
        return InputFactory::make($this->id(), $input);
    }

    private function descriptionKey()
    {
        $key = sprintf('administrator::hints.%s.%s', $this->module()->url(), $this->id);

        if (!$this->translator()->has($key)) {
            $key = sprintf('administrator::hints.%s.%s', 'global', $this->id);
        }

        return $key;
    }
}
