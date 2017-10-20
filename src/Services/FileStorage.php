<?php

namespace Terranet\Administrator\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Terranet\Administrator\Exception;
use Terranet\Administrator\Media\File;

class FileStorage
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct()
    {
        $this->filesystem = app('files');

        $this->handleStorage();
    }

    public function upload(array $files = [], $directory)
    {
        /**
         * @var $file UploadedFile
         */
        foreach ($files as $file) {
            $filename = $this->path($directory) . DIRECTORY_SEPARATOR . $file->getClientOriginalName();

            if ($this->filesystem->exists($filename) || move_uploaded_file($file->path(), $filename)) {
                return (new File($filename, $this))->toArray();
            }
        }
    }

    /**
     * Files list.
     *
     * @param $directory
     * @return Collection
     */
    public function files($directory)
    {
        return collect([])
            ->merge($this->filesystem->files($directory))
            ->map(function ($file) {
                return new File($file, $this);
            });
    }

    /**
     * Directories list.
     *
     * @param $directory
     * @return Collection
     */
    public function directories($directory)
    {
        return collect($this->filesystem->directories($directory))
            ->map(function ($file) {
                return new File($file, $this);
            });
    }

    public function mkdir($name, $basename = null)
    {
        $directory = array_filter([$this->path($basename), $name], function ($item) {
            return !is_null($item);
        });

        if ($this->filesystem->exists($directory = $this->compilePath($directory))) {
            throw new Exception(sprintf("Directory \"%s\" already exists.", $name));
        }

        if (!$this->filesystem->makeDirectory($directory)) {
            throw new Exception(sprintf("Unable to create directory \"%s\".", $name));
        }

        return $directory;
    }

    public function move($files, $target, $basedir = null)
    {
        foreach ($files as $file) {
            $this->filesystem->move(
                $this->path($this->compilePath([$basedir, $file])),
                realpath($this->path($this->compilePath([$basedir, $target]))) . DIRECTORY_SEPARATOR . $file
            );
        }
    }

    public function rename($from, $to)
    {
        $from = $this->path($from);
        $to = dirname(realpath($from)) . DIRECTORY_SEPARATOR . $to;

        if ($this->filesystem->exists($to)) {
            throw new Exception(sprintf("File %s already exists.", $to));
        }

        if (!$this->filesystem->move($from, $to)) {
            throw new Exception(sprintf("Unable to move file %s to %s.", basename($from), basename($to)));
        }

        return $to;
    }

    public function delete($files = [], $directories = [])
    {
        collect($files)->map(function ($file) {
            return $this->compilePath([$this->basename(), $file]);
        })->each(function ($file) {
            $this->filesystem->delete($file);
        });

        collect($directories)->map(function ($file) {
            return $this->compilePath([$this->basename(), $file]);
        })->each(function ($file) {
            $this->filesystem->deleteDirectory($file);
        });
    }

    /**
     * @return mixed
     */
    public function basename()
    {
        return app('scaffold.config')->get('paths.media', 'admin_media');
    }

    public function path($path = null)
    {
        return rtrim(public_path($this->basename() . DIRECTORY_SEPARATOR . $path), DIRECTORY_SEPARATOR);
    }

    protected function handleStorage()
    {
        if (!$this->filesystem->exists($storage = $this->path())) {
            if (!$this->createStorage($storage)) {
                throw new Exception("Directory {$storage} can not be created. Please create it manually and give write permissions.");
            }
        }
    }

    /**
     * @param $storage
     * @return bool
     */
    protected function createStorage($storage)
    {
        return $this->filesystem->makeDirectory($storage);
    }

    /**
     * @param $directories
     * @return string
     */
    protected function compilePath($directories)
    {
        return implode(DIRECTORY_SEPARATOR, $directories);
    }
}