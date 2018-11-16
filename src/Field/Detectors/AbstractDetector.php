<?php

namespace Terranet\Administrator\Field\Detectors;

use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Eloquent\Model;
use Terranet\Administrator\Contracts\Chainable;

abstract class AbstractDetector implements Chainable
{
    /** @var Chainable */
    protected $successor;

    /**
     * @param Chainable $successor
     */
    public function setNext(Chainable $successor)
    {
        $this->successor = $successor;
    }

    /**
     * Execute the chain.
     *
     * @param string $column
     * @param Column $metadata
     * @param Model $model
     *
     * @return null|mixed
     */
    public function __invoke(string $column, Column $metadata, Model $model)
    {
        if ($this->authorize($column, $metadata, $model)) {
            return $this->detect($column, $metadata, $model);
        }

        if ($this->successor) {
            return call_user_func_array($this->successor, compact('column', 'metadata', 'model'));
        }

        return null;
    }

    /**
     * Authorize execution.
     *
     * @param string $column
     * @param Column $metadata
     * @param Model $model
     *
     * @return bool
     */
    abstract protected function authorize(string $column, Column $metadata, Model $model): bool;

    /**
     * Detect field class.
     *
     * @param string $column
     * @param Column $metadata
     * @param Model $model
     *
     * @return mixed
     */
    abstract protected function detect(string $column, Column $metadata, Model $model);
}