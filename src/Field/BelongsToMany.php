<?php

namespace Terranet\Administrator\Field;

class BelongsToMany extends HasMany
{
    const MODE_TAGS = 'tags';
    const MODE_CHECKBOXES = 'checkboxes';

    /** @var string */
    public $icon = 'random';

    /** @var string */
    public $titleField = 'name';

    /** @var string */
    public $editMode = self::MODE_CHECKBOXES;

    /** @var bool */
    public $completeList = true;

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
     * @param  string  $column
     * @return BelongsToMany
     */
    public function useAsTitle(string $column): self
    {
        $this->titleField = $column;

        return $this;
    }

    /**
     * @return array
     */
    public function onEdit(): array
    {
        $relation = $this->relation();

        if (static::MODE_CHECKBOXES === $this->editMode && $this->completeList) {
            $values = $this->query
                ? call_user_func_array($this->query, [$relation->getRelated()->query()])
                : $relation->getRelated()->all();
        } else {
            $values = $this->value();
        }

        return [
            'relation' => $relation,
            'searchable' => \get_class($relation->getRelated()),
            'values' => $values,
            'completeList' => $this->completeList,
            'titleField' => $this->titleField,
            'editMode' => $this->editMode,
        ];
    }
}
