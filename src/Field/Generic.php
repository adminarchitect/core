<?php

namespace Terranet\Administrator\Field;

use function admin\helpers\eloquent_attribute;
use Coduo\PHPHumanizer\StringHumanizer;
use Illuminate\Database\Eloquent\Model;

abstract class Generic
{
    const PAGE_INDEX = 'index';
    const PAGE_EDIT = 'edit';
    const PAGE_VIEW = 'view';

    /** @var string */
    protected $id;

    /** @var string */
    protected $title;

    /** @var Model */
    protected $model;

    /** @var bool */
    protected $showLabel = true;

    /** @var array */
    protected $visibility = [
        self::PAGE_INDEX => true,
        self::PAGE_EDIT => true,
        self::PAGE_VIEW => true,
    ];

    /**
     * Generic constructor.
     * @param $title
     * @param null $id
     */
    private function __construct($title, $id = null)
    {
        $this->title = StringHumanizer::humanize($title);
        $this->id = snake_case($id ?: $this->title);
    }

    /**
     * @param $title
     * @param null $id
     * @return static
     */
    public static function make($title, $id = null): self
    {
        return new static($title, $id);
    }

    /**
     * @param Model $model
     * @return self
     */
    public function setModel(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Render Element.
     *
     * @param string $page
     * @return mixed
     */
    public function render(string $page = 'index')
    {
        return $this->value();
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
     * Return Element title.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param bool $showLabel
     * @return self
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
     * string $page
     * @return bool
     */
    public function isVisibleOnPage(string $page): bool
    {
        return (bool) $this->visibility[$page] ?? false;
    }

    /**
     * @param string|array $pages
     * @return self
     */
    public function hideOnPages(mixed $pages): self
    {
        return $this->setPagesVisibility((array) $pages, false);
    }

    /**
     * @param string|array $pages
     * @return self
     */
    public function showOnPages(mixed $pages): self
    {
        return $this->setPagesVisibility((array) $pages, true);
    }

    /**
     * Extract Element value.
     *
     * @return mixed
     */
    protected function value()
    {
        return $this->model->getAttribute($this->id);
    }

    /**
     * @param mixed $pages
     * @param bool $visibility
     * @return $this
     */
    protected function setPagesVisibility(mixed $pages, bool $visibility): Generic
    {
        $pages = array_diff($pages, array_keys($this->visibility));

        foreach ($pages as $page) {
            $this->visibility[$page] = $visibility;
        }

        return $this;
    }
}