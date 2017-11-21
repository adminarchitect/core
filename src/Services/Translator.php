<?php

namespace Terranet\Administrator\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Zend\Code\Generator\ValueGenerator;

class Translator
{
    /**
     * @var Collection
     */
    protected $locales;

    protected $reader;

    /**
     * @var Filesystem
     */
    protected $fs;

    public function __construct()
    {
        $this->reader = new Translations\Reader;

        $this->fs = app('files');
    }

    public function setLocales(Collection $locales)
    {
        $this->locales = $locales;

        return $this;
    }

    public function load($term = null, $only = null, $page = 1, $perPage = 20)
    {
        $files = $this->files($only);

        return (new Translations\Finder)->find(
            $this->reader->read($files, $this->locales()), $term,
            $page, $perPage
        );
    }

    public function export()
    {
        return new Translations\Exporter;
    }

    /**
     * Retrieves public filters.
     *
     * @return mixed
     */
    public function filters()
    {
        $filters = $this->files();

        if ($only = config('administrator.translations.filters.only', [])) {
            $filters = $filters->intersect($only);
        }

        if ($except = config('administrator.translations.filters.except', [])) {
            $filters = $filters->diff($except);
        }

        return $filters;
    }

    public function save($translation, $locale)
    {
        $translations = [];

        foreach ($translation as $key => $value) {
            $this->keyToArray($translations, $key, $value[$locale]);
        }

        foreach ($translations as $key => $value) {
            if (!$translations = $this->loadTranslationFile($key, $locale)) {
                continue;
            }

            $data = array_replace_recursive($translations['content'], $value);

            $content = '<?php' . str_repeat(PHP_EOL, 2) . 'return ' . $this->arrayToString($data) . ';';

            $this->fs->put($translations['path'], $content, true);
        }
    }

    protected function files($only = null): Collection
    {
        static $files = null;

        if (null === $files) {
            $path = "lang" . DIRECTORY_SEPARATOR . \localizer\locale()->iso6391();

            $files = collect(
                glob(resource_path("{$path}/*.php"))
            );

            $files = $files->map(function ($file) {
                return str_replace_last('.php', '', basename($file));
            }, $files);
        }

        return $files->when($only, function (Collection $files) use ($only) {
            return $files->intersect(is_array($only) ? $only : [$only]);
        });
    }

    protected function loadTranslationFile($file, $locale)
    {
        if (!file_exists($path = $this->reader->pathToFile($file, $locale))) {
            $this->makeFile($file, $locale);
        }

        return [
            'path' => $path,
            'content' => include_once $path,
        ];
    }

    protected function makeFile($file, $locale)
    {
        $directoryTranslationsPath = resource_path('lang' . DIRECTORY_SEPARATOR . $locale);

        if (!$this->fs->exists($directoryTranslationsPath)) {
            $this->fs->makeDirectory($directoryTranslationsPath);
        }

        $content = '<?php' . str_repeat(PHP_EOL, 2) . 'return [];';

        $this->fs->put($directoryTranslationsPath . DIRECTORY_SEPARATOR . $file . '.php', $content, true);
    }

    protected function keyToArray(&$arr, $path, $value, $separator = '.')
    {
        foreach ($keys = explode($separator, $path) as $key) {
            $arr = &$arr[$key];
        }
        $arr = $value;
    }

    protected function arrayToString($data): string
    {
        $generator = new ValueGenerator($data, ValueGenerator::TYPE_ARRAY_SHORT);
        $generator->setIndentation('    '); // 4 spaces

        return $generator->generate();
    }

    protected function locales(): Collection
    {
        return collect($this->locales);
    }
}
