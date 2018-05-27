<?php

namespace Terranet\Administrator\Traits;

use Closure;
use Symfony\Component\Finder\SplFileInfo;

trait ResolvesClasses
{
    /**
     * Resolves and Initializes Class by filename.
     *
     * @param SplFileInfo $fileInfo
     * @param null|Closure $callback
     */
    public function resolveClass(SplFileInfo $fileInfo, Closure $callback = null)
    {
        /** @noinspection PhpIncludeInspection */
        require_once $fileInfo->getPathname();

        $instance = app()->make($this->getModuleClassName($fileInfo));

        if (is_callable($callback)) {
            $callback($instance);
        }
    }

    /**
     * @param SplFileInfo $fileInfo
     *
     * @return mixed
     */
    protected function getModuleClassName(SplFileInfo $fileInfo)
    {
        $name = str_replace('.php', '', $fileInfo->getBasename());

        $path = trim(str_replace(
            app_path(),
            '',
            dirname($fileInfo->getPathname())
        ), DIRECTORY_SEPARATOR);

        $location = str_replace('/', '\\', $path);

        return app()->getNamespace()."{$location}\\{$name}";
    }
}
