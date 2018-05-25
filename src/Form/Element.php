<?php

namespace Terranet\Administrator\Form;

use Terranet\Administrator\Contracts\Form\Element as HtmlElement;
use Terranet\Administrator\Contracts\Form\Relationship;
use Terranet\Administrator\Contracts\Form\Validable;
use Terranet\Administrator\Traits\Form\FormControl;
use Terranet\Administrator\Traits\Form\HasRelation;
use Terranet\Administrator\Traits\Form\RendersTranslatableElement;
use Terranet\Administrator\Traits\Form\ValidatesFormElement;

abstract class Element implements HtmlElement, Validable, Relationship
{
    use RendersTranslatableElement, FormControl, ValidatesFormElement, HasRelation {
        RendersTranslatableElement::html as translatableHtml;
    }

    protected $translatable = false;

    protected $view = null;

    protected $viewParams = [];

    /**
     * @var mixed
     */
    protected $defaultValue;

    public function __construct($name, array $attributes = [])
    {
        $this->setName($name);

        $this->setAttributes($attributes);
    }

    public function getView()
    {
        return $this->view;
    }

    public function getViewParams()
    {
        return (array) $this->viewParams;
    }

    public function setView($name, array $params = [])
    {
        $this->view = (string) $name;

        $this->viewParams = $params;

        return $this;
    }

    /**
     * Init element from set of attributes.
     *
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        $this->validateAttributes();

        $this->decoupleOptionsFromAttributes();

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setTranslatable($flag = true)
    {
        $this->translatable = (bool) $flag;

        return $this;
    }

    public function getTranslatable()
    {
        return $this->translatable;
    }

    final public function html()
    {
        if (!$this->value) {
            $this->populateValue();
        }

        if ($this->translatable) {
            return $this->translatableHtml();
        }

        if ($renderer = $this->getView()) {
            $params = $this->getViewParams();

            # prevent recursion call
            # when inside of view there is a call like: $element->html()
            $this->setView(null);

            $html = (string) view($renderer, ['element' => $this] + $params);

            # restore view
            $this->setView($renderer);
        } else {
            $html = $this->render();
        }

        return $html . $this->errors();
    }

    /**
     * Each subclass should have this method realized.
     *
     * @return mixed
     */
    abstract public function render();

    /**
     * @param mixed $value
     * @return $this
     */
    public function setDefaultValue($value)
    {
        $this->defaultValue = $value;

        return $this;
    }

    protected function populateValue()
    {
        /**
         * If relation detected => try to extract value from relation or magnet link
         */
        if ($this->hasRelation() && ($repository = $this->getRepository())) {
            if (!$value = $this->extractValueFromEloquentRelation($repository)) {
                if ($magnet = $this->isMagnetParameter()) {
                    $value = $magnet[$this->getName()];
                }
            }

            return $this->setValue($value);
        }

        /**
         * Try to extract value from Closure provided by form configuration
         * Note: checking for function_exists is set to ensure that \Closure is provided
         * and protect calling functions when provided value is something like 'rand' which is also is_callable
         */
        if (is_callable($closure = $this->getValue())) {
            if (!(is_string($closure) && function_exists($closure))) {
                $value = call_user_func($closure, $this->getRepository());

                return $this->setValue($value);
            }
        }

        /**
         * Set default value from Eloquent model.
         */
        if (($element = $this->getRepository()) && $element->exists) {
            if ($value = $element->getAttribute($this->name)) {
                return $this->setValue($value);
            }
        }

        /**
         * If column configured as a magnet link, so try to extract value form Request
         */
        if ($magnet = $this->isMagnetParameter()) {
            return $this->setValue($magnet[$this->getName()]);
        }

        if ($this->defaultValue) {
            return $this->setValue($this->defaultValue);
        }

        return null;
    }

    public function getRepository()
    {
        return app('scaffold.model') ?: app('scaffold.module')->model();
    }

    protected function isMagnetParameter()
    {
        return array_key_exists(
            $this->getName(),
            $magnet = app('scaffold.magnet')->toArray()
        ) ? $magnet : false;
    }
}
