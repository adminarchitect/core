<?php

namespace Terranet\Administrator\Field;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Terranet\Administrator\Architect;
use Terranet\Administrator\Contracts\AutoTranslatable;
use Terranet\Administrator\Contracts\Sortable;
use Terranet\Administrator\Field\Traits\AcceptsCustomFormat;
use Terranet\Administrator\Field\Traits\AppliesSorting;
use Terranet\Administrator\Field\Traits\HandlesVisibility;
use Terranet\Administrator\Field\Traits\HasValuePresenter;
use Terranet\Administrator\Scaffolding;
use Terranet\Administrator\Traits\AutoTranslatesInstances;

abstract class Field implements Sortable, AutoTranslatable
{
    use AcceptsCustomFormat, AppliesSorting, AutoTranslatesInstances, HasValuePresenter, HandlesVisibility;

    /** @var string */
    protected $id;

    /** @var string */
    protected $title;

    /** @var string */
    protected $name;

    /** @var mixed */
    protected $value;

    /** @var string */
    protected $description;

    /** @var Model */
    protected $model;

    /** @var bool */
    protected $showLabel = true;

    /** @var array */
    protected $visibility = [
        Scaffolding::PAGE_INDEX => true,
        Scaffolding::PAGE_EDIT => true,
        Scaffolding::PAGE_VIEW => true,
    ];

    /** @var array */
    protected $attributes = [
        'class' => 'form-control',
    ];

    /**
     * Field constructor.
     *
     * @param  string  $title
     * @param  null|string  $id
     */
    private function __construct(string $title, string $id = null)
    {
        $this->setId(
            $id ?: Str::snake($title)
        );

        if (trans()->has($key = $this->translationKey())) {
            $this->setTitle((string) trans()->get($key));
        } else {
            $this->setTitle(Architect::humanize($title));
        }

        if (trans()->has($key = $this->descriptionKey())) {
            $this->setDescription((string) trans()->get($key));
        }
    }

    /**
     * @param $title
     * @param  null  $id
     * @param  \Closure  $callback
     * @return static
     */
    public static function make($title, $id = null, \Closure $callback = null): self
    {
        $instance = new static($title, $id);

        if (null !== $callback) {
            $callback->call($instance, $instance);
        }

        return $instance;
    }

    /**
     * Create new element from another.
     *
     * @param  Field  $element
     * @return static
     */
    public static function makeFrom(self $element): self
    {
        return static::make($element->title(), $element->id());
    }

    /**
     * Switch to a new element type.
     *
     * @param  string  $className
     * @return mixed
     */
    public function switchTo(string $className)
    {
        return forward_static_call_array([$className, 'make'], [$this->title(), $this->id()]);
    }

    /**
     * @param  Model  $model
     * @return static
     */
    public function setModel(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Render Element.
     *
     * @param  string  $page
     * @return mixed
     */
    final public function render(string $page = 'index')
    {
        if (\in_array($page, [Scaffolding::PAGE_INDEX, Scaffolding::PAGE_VIEW], true)) {
            if ($this->hasCustomFormat()) {
                return $this->callFormatter($this->model, $page);
            }

            if ($presenter = $this->hasPresenter($this->model, $this->id())) {
                return $this->callPresenter($presenter);
            }
        }

        $data = [
            'field' => $this,
            'model' => $this->model,
            'attributes' => $this->getAttributes(),
        ];

        if (method_exists($this, $dataGetter = 'on'.Str::title($page))) {
            $data += \call_user_func([$this, $dataGetter]);
        }

        if (View::exists($view = $this->template($page))) {
            return View::make($view, $data);
        }

        return View::make($this->template($page, 'Key'), $data);
    }

    /**
     * Return Element ID.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Form name.
     *
     * @return string
     */
    public function name(): string
    {
        if (null === $this->name) {
            $parts = explode('.', $this->id());

            if (\count($parts) > 1) {
                $first = Arr::first($parts);
                $other = \array_slice($parts, 1);

                $other = array_map(function ($part) {
                    return "[$part]";
                }, $other);

                return $this->name = implode('', array_merge([$first], $other));
            }

            return $this->name = $this->id();
        }

        return $this->name;
    }

    /**
     * @param  string  $name
     * @return Field
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param  string  $id
     * @return static
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Return Element title.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param  string  $title
     * @return static
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param  null|string  $description
     * @return static
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Make element translatable.
     *
     * @return Translatable
     */
    public function translatable()
    {
        return Translatable::make($this);
    }

    /**
     * @param  bool  $hideLabel
     * @return static
     */
    public function hideLabel(bool $hideLabel): self
    {
        $this->showLabel = !$hideLabel;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHiddenLabel(): bool
    {
        return !$this->showLabel;
    }

    /**
     * string $page.
     *
     * @param  string  $page
     * @return bool
     */
    public function isVisibleOnPage(string $page): bool
    {
        return (bool) $this->visibility[$page] ?? false;
    }

    /**
     * @param  array|string  $pages
     * @return static
     */
    public function hideOnPages($pages): self
    {
        return $this->setPagesVisibility((array) $pages, false);
    }

    /**
     * @param  array|string  $pages
     * @return static
     */
    public function showOnPages($pages): self
    {
        return $this->setPagesVisibility((array) $pages, true);
    }

    /**
     * Make column sortable.
     *
     * @param  null|\Closure  $callback
     * @return static
     */
    public function sortable(\Closure $callback = null): self
    {
        app('scaffold.module')->addSortable(
            $this->id(),
            $callback
        );

        return $this;
    }

    /**
     * Remove column from Sortable collection.
     *
     * @return static
     */
    public function disableSorting(): self
    {
        app('scaffold.module')->removeSortable($this->id());

        return $this;
    }

    /**
     * Set value.
     *
     * @param $value
     * @return Field
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Extract Element value.
     *
     * @return mixed
     */
    public function value()
    {
        if (null !== $this->value) {
            return $this->value;
        }

        if (!$this->model) {
            return null;
        }

        $property = last(explode('.', $this->id));
        $val = $this->model->getAttribute($property);

        if ($val instanceof \BenSampo\Enum\Enum) {
            $val = $val->value;
        }

        return $val;
    }

    /**
     * @param $key
     * @param  null  $value
     * @param  mixed  $attribute
     * @return static
     */
    public function setAttribute($attribute, $value = null): self
    {
        if (\is_array($attribute)) {
            foreach ($attribute as $key => $value) {
                $this->setAttribute($key, $value);
            }
        } else {
            $this->attributes[$attribute] = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    public function translationKey()
    {
        $key = sprintf('administrator::columns.%s.%s', $this->translatableModule()->url(), $this->id);

        if (!trans()->has($key)) {
            $key = sprintf('administrator::columns.%s.%s', 'global', $this->id);
        }

        return $key;
    }

    /**
     * @return string
     */
    public function descriptionKey()
    {
        $key = sprintf('administrator::hints.%s.%s', $this->translatableModule()->url(), $this->id);

        if (!trans()->has($key)) {
            $key = sprintf('administrator::hints.%s.%s', 'global', $this->id);
        }

        return $key;
    }

    /**
     * @param  \Closure  $condition
     * @return self
     */
    public function when(\Closure $condition): self
    {
        $this->when = $condition;

        return $this;
    }

    /**
     * @param  mixed  $pages
     * @param  bool  $visibility
     * @return $this
     */
    protected function setPagesVisibility($pages, bool $visibility): self
    {
        $pages = array_intersect($pages, array_keys($this->visibility));

        foreach ($pages as $page) {
            $this->visibility[$page] = $visibility;
        }

        return $this;
    }

    /**
     * @param  string  $page
     * @param  string  $field
     * @return string
     */
    protected function template(string $page, string $field = null): string
    {
        return sprintf(
            'administrator::fields.%s.%s',
            Str::snake($field ?? class_basename($this)),
            $page
        );
    }
}
