<?php

namespace Terranet\Administrator\Services\Translations;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class Exporter
{
    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var Collection
     */
    protected $locales;

    public function __construct()
    {
        $this->fs = app('files');
    }

    public function toJS(Collection $locales, $to = null): array
    {
        $exported = [];

        if ($translations = $this->load($locales)) {
            $translations = $this->replaceAttributes($translations);

            $to = $to ?: public_path('js'.DIRECTORY_SEPARATOR.'translations');

            if (!$this->fs->exists($to)) {
                $this->fs->makeDirectory($to);
            }

            foreach ($translations as $lang => $translation) {
                $content = 'window.messages = {"'.$lang.'":'.json_encode($translation).'};';
                file_put_contents($file = $to.DIRECTORY_SEPARATOR.$lang.'.js', $content);
                $exported[] = $file;
            }
        }

        return $exported;
    }

    protected function load(Collection $locales)
    {
        $data = [];
        $path = resource_path('lang'.DIRECTORY_SEPARATOR);

        foreach ($locales as $locale) {
            if (!$this->fs->exists($path.$locale->iso6391())) {
                continue;
            }

            if ($files = $this->fs->files($path.$locale->iso6391())) {
                foreach ($files as $file) {
                    $fileName = str_replace('.php', '', $file->getFilename());
                    $data[$locale->iso6391()][$fileName] = trans($fileName, [], $locale->iso6391());
                }
            }
        }

        return $data;
    }

    protected function replaceAttributes($translations)
    {
        foreach ($translations as &$content) {
            if (is_array($content)) {
                $content = $this->replaceAttributes($content);

                continue;
            }

            $content = preg_replace('%:([a-z\_]+)%m', '{$1}', $content);
        }

        return $translations;
    }
}
