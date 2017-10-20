<?php

namespace Terranet\Administrator\Media;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use SplFileInfo;
use Terranet\Administrator\Services\FileStorage;

class File extends SplFileInfo implements Arrayable
{
    /**
     * @var
     */
    private $storage;

    /**
     * File constructor.
     *
     * @param $file
     * @param FileStorage $storage
     */
    public function __construct($file, FileStorage $storage)
    {
        parent::__construct($file);

        $this->storage = $storage;
    }

    public function getIcon()
    {
        $icons = new Icons;
        foreach ($icons->table() as $group) {
            if ((new MimeType($this))->is($group)) {
                return $icons->icon($group);
            }
        }

        return 'fa fa-file-text-o';
    }

    public function toArray()
    {
        return [
            'path' => $path = trim(str_replace($this->storage->path(), '', $this->getPathname()), DIRECTORY_SEPARATOR),
            'url' => $this->fullUrl($path),
            'createdAt' => $this->createdAt(),
            'updatedAt' => $this->updatedAt(),
            'size' => $this->getSize(),
            'dirname' => $this->getPathInfo()->getFilename(),
            'basename' => $basename = $this->getBasename(),
            'extension' => $ext = $this->getExtension(),
            'filename' => preg_replace('~\.' . $ext . '$~si', '', $basename),
            'icon' => $this->getIcon(),
            'isDir' => $this->isDir(),
            'isFile' => $this->isFile(),
            'isImage' => $this->isImage(),
        ];
    }

    /**
     * @return bool
     */
    public function isImage()
    {
        return (new MimeType($this))->isImage();
    }

    /**
     * @param $path
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function fullUrl($path)
    {
        return url($this->storage->basename() . '/' . $path);
    }

    /**
     * @return string
     */
    public function createdAt(): string
    {
        return Carbon::createFromTimestamp($this->getCTime())->toDayDateTimeString();
    }

    /**
     * @return string
     */
    public function updatedAt(): string
    {
        return Carbon::createFromTimestamp($this->getMTime())->toDayDateTimeString();
    }
}