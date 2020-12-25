<?php

namespace Terranet\Administrator\Field;

use Terranet\Administrator\Architect;
use Terranet\Administrator\Services\MediaLibraryProvider;

class Media extends Field
{
    /** @var string */
    public $collection = 'default';

    /** @var string */
    public $conversion = '';

    /** @var int */
    public $perPage = 10;

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

    protected function onIndex(): array
    {
        return [
            'count' => MediaLibraryProvider::forModel($this->model)->count($this->collection),
            'module' => Architect::resourceByEntity($this->model) ?: app('scaffold.module'),
        ];
    }
}
