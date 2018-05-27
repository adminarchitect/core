<?php

namespace Terranet\Administrator\Filters;

use Terranet\Administrator\Contracts\Form\Queryable;
use Terranet\Administrator\Filters\Element\Text;
use Terranet\Administrator\Filters\InputFactory as FilterInputFactory;
use Terranet\Administrator\Form\FormElement;

/**
 * Class FilterElement.
 *
 *
 * @method static FilterElement text(string $name)
 * @method static FilterElement search(string $name)
 * @method static FilterElement number(string $name)
 * @method static FilterElement select(string $name, array $attributes, array $options)
 * @method static FilterElement daterange(string $name)
 * @method static FilterElement date(string $name)
 * @method static FilterElement datalist(string $name, array $attributes, array $options)
 */
class FilterElement extends FormElement
{
    /**
     * Init FormElement object by calling static method.
     *
     * @example: FilterElement::text('title')
     *
     * @param $inputType
     * @param $arguments
     * @note:
     *  1st argument = name,
     *  2nd argument = html options,
     *  3rd argument = values (select, radio, multicheckbox)
     *
     * @return
     */
    public static function __callStatic($inputType, $arguments)
    {
        $name = $arguments[0];

        $inputType = FilterInputFactory::make($name, $inputType);

        $element = (new static($name))->setInput(
            $inputType
        );

        $input = $element->getInput();

        if (is_array($attributes = array_get($arguments, 1))) {
            $input->setAttributes($attributes);
        }

        if (is_array($options = array_get($arguments, 2)) && !empty($options)
            && method_exists($input, 'setOptions')
        ) {
            $input->setOptions($options);
        }

        return $element;
    }

    /**
     * Create a custom Filter element.
     *
     * @param $id
     * @param mixed string|Queryable $input
     *
     * @return mixed
     */
    public static function custom($id, $input)
    {
        return (new static($id))->setInput($input instanceof Queryable ? $input : new $input($id));
    }

    /**
     * Init default input type.
     *
     * @param $id
     *
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
     *
     * @return mixed
     */
    protected function makeInput($input)
    {
        return FilterInputFactory::make($this->id(), $input);
    }
}
