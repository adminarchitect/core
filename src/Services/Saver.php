<?php

namespace Terranet\Administrator\Services;

use Illuminate\Database\Eloquent\Model;
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
use Terranet\Administrator\Requests\UpdateRequest;
use function admin\db\scheme;

class Saver implements SaverContract
{
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
     * @param UpdateRequest $request
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

                if ($this->isKey($field) || $this->isMediaFile($field)) {
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

            /*
            |-------------------------------------------------------
            | Save main data
            |-------------------------------------------------------
            */
            $this->save();

            /*
            |-------------------------------------------------------
            | Relationships
            |-------------------------------------------------------
            | Save related data, fetched by "relation" from related tables
            */
            $this->saveRelations();

            $this->saveMedia();

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
        return app('scaffold.form');
    }

    /**
     * @param $field
     *
     * @return bool
     */
    protected function isKey($field)
    {
        return $field instanceof Id;
    }

    /**
     * @param $field
     *
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
        $this->data = array_except($this->data, [
            '_token',
            'save',
            'save_create',
            'save_return',
            $this->repository->getKeyName(),
        ]);

        // leave only fillable columns
        $this->data = array_only($this->data, $this->repository->getFillable());
    }

    /**
     * Persist data.
     */
    protected function save()
    {
        $this->nullifyEmptyNullables($this->repository->getTable());

        $this->repository->fill(
            $this->protectAgainstNullPassword()
        )->save();
    }

    /**
     * Save relations.
     */
    protected function saveRelations()
    {
        if (!empty($this->relations)) {
            foreach ($this->relations as $name => $field) {
                $relation = \call_user_func([$this->repository, $name]);

                switch (\get_class($field)) {
                    case BelongsTo::class:
                        // @var \Illuminate\Database\Eloquent\Relations\BelongsTo $relation
                        $relation->associate(
                            $this->request->get($relation->getForeignKey())
                        );

                        break;
                    case HasOne::class:
                        /** @var \Illuminate\Database\Eloquent\Relations\HasOne $relation */
                        $related = $relation->getResults();

                        $related && $related->exists
                            ? $relation->update($this->request->get($name))
                            : $relation->create($this->request->get($name));

                        break;
                    case BelongsToMany::class:
                        $values = array_map('intval', $this->request->get($name, []));
                        $relation->sync($values);

                        break;
                    default:
                        break;
                }
            }

            $this->repository->save();
        }
    }

    /**
     * Process Media.
     */
    protected function saveMedia()
    {
        if ($this->repository instanceof HasMedia) {
            $media = (array) $this->request['_media_'];

            if (!empty($trash = array_get($media, '_trash_', []))) {
                $this->repository->media()->whereIn(
                    'id',
                    $trash
                )->delete();
            }

            foreach (array_except($media, '_trash_') as $collection => $objects) {
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
     *
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
     *
     * @return mixed
     */
    protected function handleJsonType($name, $value)
    {
        if ($cast = array_get($this->repository->getCasts(), $name)) {
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
     * @param null $default
     *
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
        if (array_has($this->data, 'password') && empty($this->data['password'])) {
            unset($this->data['password']);
        }

        return $this->data;
    }

    /**
     * @param $field
     *
     * @return bool
     */
    protected function isBoolean($field)
    {
        return $field instanceof Boolean;
    }

    /**
     * @param $field
     *
     * @return bool
     */
    protected function isMediaFile($field)
    {
        return $field instanceof Media;
    }
}
