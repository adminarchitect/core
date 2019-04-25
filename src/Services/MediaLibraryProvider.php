<?php

namespace Terranet\Administrator\Services;

use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaLibraryProvider
{
    const COLLECTION_DEFAULT = 'default';

    /** @var HasMedia */
    protected $model;

    /**
     * MediaLibraryProvider constructor.
     * @param HasMedia $model
     */
    protected function __construct(HasMedia $model)
    {
        $this->model = $model;
    }

    /**
     * @param HasMedia $model
     * @return MediaLibraryProvider
     */
    public static function forModel(HasMedia $model): MediaLibraryProvider
    {
        return new static($model);
    }

    /**
     * Calculate items count per collection.
     *
     * @param string $collection
     * @return mixed
     */
    public function count(string $collection = self::COLLECTION_DEFAULT)
    {
        return $this->model->media()->where('collection_name', $collection)->count();
    }

    /**
     * Fetch all media in collection.
     *
     * @param string $collection
     * @param int $perPage
     * @return mixed
     */
    public function fetch(string $collection = self::COLLECTION_DEFAULT, $perPage = 20)
    {
        return $this->model->media()
            ->where('collection_name', $collection)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * @param UploadedFile $file
     * @param string $collection
     * @return \Spatie\MediaLibrary\Models\Media
     */
    public function attach(UploadedFile $file, string $collection): Media
    {
        return $this->model->addMedia($file)->toMediaCollection($collection);
    }

    /**
     * @param $mediaId
     * @return mixed
     */
    public function detach($mediaId)
    {
        return $this->model->deleteMedia($mediaId);
    }

    /**
     * Provide extra information.
     *
     * @param Media $item
     * @return Media
     */
    public static function toJson(Media $item)
    {
        $item->url = $item->getUrl();
        $item->conversions = array_build(
            $item->getMediaConversionNames(),
            function ($key, $conversion) use ($item) {
                return [$conversion, $item->getUrl($conversion)];
            }
        );

        return $item;
    }
}
