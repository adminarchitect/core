<?php

namespace Terranet\Administrator\Services\Translations;

use Illuminate\Support\Collection;
use Terranet\Localizer\Locale;

class Reader
{
    /**
     * Reads the translation file's contents.
     *
     * @param Collection $files
     * @param Collection $locales
     * @return mixed
     */
    public function read(Collection $files, Collection $locales)
    {
        return $locales->reduce(function ($translations, $locale) use ($files) {
            $files->each(function ($file) use ($locale, &$translations) {
                if (file_exists($path = $this->pathToFile($file, $locale))) {
                    $content[$file] = include_once $path;

                    foreach (array_dot($content) as $key => $value) {
                        $translations[$key][$locale->iso6391()] = $value ? $value : '';
                    }
                }
            });

            return $translations;
        }, []);
    }

    /**
     * Retrieves the real path to a translation file.
     *
     * @param $file
     * @param $locale
     * @return string
     */
    public function pathToFile($file, $locale): string
    {
        return resource_path('lang' . DIRECTORY_SEPARATOR . (is_a($locale, Locale::class) ? $locale->iso6391() : $locale) . DIRECTORY_SEPARATOR . $file . '.php');
    }
}