<?php

namespace Terranet\Administrator\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Terranet\Administrator\Services\Translations\Reader;
use Zend\Code\Generator\ValueGenerator;

class TranslationsManager
{
    /** @var Collection */
    protected $locales;

    /** @var Translations\Reader */
    protected $reader;

    /** @var Filesystem */
    protected $filesystem;

    /**
     * TranslationsManager constructor.
     */
    public function __construct(Reader $translationsReader, Filesystem $filesystem)
    {
        $this->reader = $translationsReader;
        $this->filesystem = $filesystem;
    }

    /**
     * @param Collection $locales
     * @return $this
     */
    public function setLocales(Collection $locales)
    {
        $this->locales = $locales;

        return $this;
    }

    /**
     * @param null $term
     * @param null $only
     * @param int $page
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function load($term = null, $only = null, $page = 1, $perPage = 20)
    {
        return (new Translations\Finder())->find(
            $this->reader->read($this->files($only), $this->locales()),
            $term, $page, $perPage
        );
    }

    /**
     * @return Translations\Exporter
     */
    public function exporter()
    {
        return new Translations\Exporter();
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

    /**
     * @param $translation
     * @param $locale
     */
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

            $content = '<?php'.str_repeat(PHP_EOL, 2).'return '.$this->arrayToString($data).';';

            $this->filesystem->put($translations['path'], $content, true);
        }
    }

    /**
     * @param null $only
     * @return Collection
     */
    protected function files($only = null): Collection
    {
        static $files = null;

        if (null === $files) {
            $path = 'lang'.\DIRECTORY_SEPARATOR.\localizer\locale()->iso6391();

            $files = collect(
                glob(resource_path("{$path}/*.php"))
            );

            $files = $files->map(function ($file) {
                return str_replace_last('.php', '', basename($file));
            }, $files);
        }

        return $files->when($only, function (Collection $files) use ($only) {
            return $files->intersect(\is_array($only) ? $only : [$only]);
        });
    }

    /**
     * @param $file
     * @param $locale
     * @return array
     */
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

    /**
     * @param $file
     * @param $locale
     */
    protected function makeFile($file, $locale)
    {
        $directoryTranslationsPath = resource_path('lang'.\DIRECTORY_SEPARATOR.$locale);

        if (!$this->filesystem->exists($directoryTranslationsPath)) {
            $this->filesystem->makeDirectory($directoryTranslationsPath);
        }

        $content = '<?php'.str_repeat(PHP_EOL, 2).'return [];';

        $this->filesystem->put($directoryTranslationsPath.\DIRECTORY_SEPARATOR.$file.'.php', $content, true);
    }

    /**
     * @param $arr
     * @param $path
     * @param $value
     * @param string $separator
     */
    protected function keyToArray(&$arr, $path, $value, $separator = '.')
    {
        foreach ($keys = explode($separator, $path) as $key) {
            $arr = &$arr[$key];
        }
        $arr = $value;
    }

    /**
     * @param $data
     * @return string
     */
    protected function arrayToString($data): string
    {
        $generator = new ValueGenerator($data, ValueGenerator::TYPE_ARRAY_SHORT);
        $generator->setIndentation('    '); // 4 spaces

        return $generator->generate();
    }

    /**
     * @return Collection
     */
    protected function locales(): Collection
    {
        return collect($this->locales);
    }
}
