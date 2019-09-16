<?php

namespace Terranet\Administrator\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Terranet\Administrator\Contracts\Services\Saver as SaverContract;
use Terranet\Administrator\Field\BelongsTo;
use Terranet\Administrator\Field\BelongsToMany;
use Terranet\Administrator\Field\Boolean;
use Terranet\Administrator\Field\File;
use Terranet\Administrator\Field\HasMany;
use Terranet\Administrator\Field\HasOne;
use Terranet\Administrator\Field\Id;
use Terranet\Administrator\Field\Image;
use Terranet\Administrator\Field\Media;
use Terranet\Administrator\Field\Traits\HandlesRelation;
use Terranet\Administrator\Requests\UpdateRequest;
use Terranet\Translatable\Translatable;
use function admin\db\scheme;

class Saver implements SaverContract
{
    use HandlesRelation;

    /**
     * Data collected during saving process.
     *
     * @var array
     */
    protected $data = [];

    /**
     * List of relations queued for saving.
     *
     * @var array
     */
    protected $relations = [];

    /**
     * Main module repository.
     *
     * @var Model
     */
    protected $repository;

    /**
     * @var UpdateRequest
     */
    protected $request;

    /**
     * Saver constructor.
     *
     * @param               $eloquent
     * @param  UpdateRequest  $request
     */
    public function __construct($eloquent, UpdateRequest $request)
    {
        $this->repository = $eloquent;
        $this->request = $request;
    }

    /**
     * Process request and persist data.
     *
     * @return mixed
     */
    public function sync()
    {
        $this->connection()->transaction(function () {
            foreach ($this->editable() as $field) {
                // get original HTML input
                $name = $field->id();

                if ($this->isKey($field) || $this->isMediaFile($field) || $this->isTranslatable($field)) {
                    continue;
                }

                if ($this->isRelation($field)) {
                    $this->relations[$name] = $field;
                }

                $value = $this->isFile($field) ? $this->request->file($name) : $this->request->get($name);

                $value = $this->isBoolean($field) ? (bool) $value : $value;

                $value = $this->handleJsonType($name, $value);

                $this->data[$name] = $value;
            }

            $this->cleanData();

            $this->collectTranslatable();

            $this->appendTranslationsToRelations();

            Model::unguard();

            $this->process();

            Model::reguard();
        });

        return $this->repository;
    }

    /**
     * Fetch editable fields.
     *
     * @return mixed
     */
    protected function editable()
    {
        return app('scaffold.module')->form();
    }

    /**
     * @param $field
     * @return bool
     */
    protected function isKey($field)
    {
        return $field instanceof Id;
    }

    /**
     * @param $field
     * @return bool
     */
    protected function isFile($field)
    {
        return $field instanceof File || $field instanceof Image;
    }

    /**
     * Protect request data against external data.
     */
    protected function cleanData()
    {
        $this->data = Arr::except($this->data, [
            '_token',
            'save',
            'save_create',
            'save_return',
            $this->repository->getKeyName(),
        ]);

        // clean from relationable fields.
        $this->data = array_diff_key($this->data, $this->relations);
    }

    /**
     * Persist main eloquent model including relations & media.
     */
    protected function process()
    {
        $this->nullifyEmptyNullables($this->repository->getTable());

        \DB::transaction(function () {
            $this->repository->fill(
                $this->protectAgainstNullPassword()
            )->save();

            $this->saveRelations();

            $this->saveMedia();
        });
    }

    /**
     * Save relations.
     */
    protected function saveRelations()
    {
        foreach ($this->relations as $name => $field) {
            $relation = \call_user_func([$this->repository, $name]);

            switch (\get_class($field)) {
                case BelongsTo::class:
                    // @var \Illuminate\Database\Eloquent\Relations\BelongsTo $relation
                    $relation->associate(
                        $this->request->get($this->getForeignKey($relation))
                    );

                    break;
                case HasOne::class:
                    /** @var \Illuminate\Database\Eloquent\Relations\HasOne $relation */
                    $related = $relation->getResults();

                    $related && $related->exists
                        ? $related->update($this->request->get($name))
                        : $this->repository->{$name}()->create($this->request->get($name));

                    break;
                case BelongsToMany::class:
                    $values = array_map('intval', $this->request->get($name, []));
                    $relation->sync($values);

                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Process Media.
     */
    protected function saveMedia()
    {
        if ($this->repository instanceof HasMedia) {
            $media = (array) $this->request['_media_'];

            if (!empty($trash = Arr::get($media, '_trash_', []))) {
                $this->repository->media()->whereIn(
                    'id',
                    $trash
                )->delete();
            }

            foreach (Arr::except($media, '_trash_') as $collection => $objects) {
                foreach ($objects as $uploadedFile) {
                    $this->repository->addMedia($uploadedFile)->toMediaCollection($collection);
                }
            }
        }
    }

    /**
     * Remove null values from data.
     *
     * @param $relation
     * @param $values
     * @return array
     */
    protected function forgetNullValues($relation, $values)
    {
        $keys = explode('.', $this->getQualifiedRelatedKeyName($relation));
        $key = array_pop($keys);

        return array_filter((array) $values[$key], function ($value) {
            return null !== $value;
        });
    }

    /**
     * Collect relations for saving.
     *
     * @param $field
     */
    protected function isRelation($field)
    {
        return ($field instanceof BelongsTo)
            || ($field instanceof HasOne)
            || ($field instanceof HasMany)
            || ($field instanceof BelongsToMany);
    }

    /**
     * @param $this
     */
    protected function appendTranslationsToRelations()
    {
        if (!empty($this->relations)) {
            foreach (array_keys((array) $this->relations) as $relation) {
                if ($translations = $this->input("{$relation}.translatable")) {
                    $this->relations[$relation] += $translations;
                }
            }
        }
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    protected function handleJsonType($name, $value)
    {
        if ($cast = Arr::get($this->repository->getCasts(), $name)) {
            if (\in_array($cast, ['array', 'json'], true)) {
                $value = json_decode($value);
            }
        }

        return $value;
    }

    /**
     * Collect translations.
     */
    protected function collectTranslatable()
    {
        foreach ($this->request->get('translatable', []) as $key => $value) {
            $this->data[$key] = $value;
        }
    }

    /**
     * Get database connection.
     *
     * @return \Illuminate\Foundation\Application|mixed
     */
    protected function connection()
    {
        return app('db');
    }

    /**
     * Retrieve request input value.
     *
     * @param $key
     * @param  null  $default
     * @return mixed
     */
    protected function input($key, $default = null)
    {
        return app('request')->input($key, $default);
    }

    /**
     * Set empty "nullable" values to null.
     *
     * @param $table
     */
    protected function nullifyEmptyNullables($table)
    {
        $columns = scheme()->columns($table);

        foreach ($this->data as $key => &$value) {
            if (!array_key_exists($key, $columns)) {
                continue;
            }

            if (!$columns[$key]->getNotnull() && empty($value)) {
                $value = null;
            }
        }
    }

    /**
     * Ignore empty password from being saved.
     *
     * @return array
     */
    protected function protectAgainstNullPassword(): array
    {
        if (Arr::has($this->data, 'password') && empty($this->data['password'])) {
            unset($this->data['password']);
        }

        return $this->data;
    }

    /**
     * @param $field
     * @return bool
     */
    protected function isBoolean($field)
    {
        return $field instanceof Boolean;
    }

    /**
     * @param $field
     * @return bool
     */
    protected function isMediaFile($field)
    {
        return $field instanceof Media;
    }

    /**
     * @param $field
     * @return bool
     */
    protected function isTranslatable($field)
    {
        return $field instanceof Translatable;
    }
}
