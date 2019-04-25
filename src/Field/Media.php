<?php

namespace Terranet\Administrator\Field;

use Terranet\Administrator\Architect;
use Terranet\Administrator\Services\MediaLibraryProvider;

class Media extends Field
{
    /** @var string */
    protected $collection = 'default';

    /** @var string */
    protected $conversion = '';

    /** @var int */
    protected $perPage = 10;

    /**
     * @param string $collection
     * @return $this
     */
    public function fromCollection(string $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @param string $conversion
     *
     * @return self
     */
    public function convertedTo(string $conversion): self
    {
        $this->conversion = $conversion;

        return $this;
    }

    /**
     * @return array
     */
    protected function onIndex(): array
    {
        return [
            'count' => MediaLibraryProvider::forModel($this->model)->count($this->collection),
            'module' => Architect::resourceByEntity($this->model) ?: app('scaffold.module'),
        ];
    }

    /**
     * @return array
     */
    protected function onView(): array
    {
        return [
            'collection' => $this->collection,
            'conversion' => $this->conversion,
        ];
    }

    /**
     * @return array
     */
    protected function onEdit(): array
    {
        return [];
    }
}
