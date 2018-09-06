<?php

namespace Terranet\Administrator\Field;

use Terranet\Administrator\Modules\Faked;

class BelongsToMany extends HasMany
{
    const MODE_TAGS = 'tags';
    const MODE_CHECKBOXES = 'checkboxes';

    /** @var string */
    protected $icon = 'random';

    /** @var string */
    protected $titleField = 'name';

    /** @var string */
    protected $editMode = self::MODE_CHECKBOXES;

    /** @var bool */
    protected $completeList = true;

    /**
     * Show editable controls as checkboxes.
     *
     * @return self
     */
    public function tagList(): self
    {
        $this->editMode = static::MODE_TAGS;
        $this->completeList = false;

        return $this;
    }

    /**
     * @param string $column
     * @return BelongsToMany
     */
    public function useAsTitle(string $column): BelongsToMany
    {
        $this->titleField = $column;

        return $this;
    }
}
