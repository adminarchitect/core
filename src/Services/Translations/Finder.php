<?php

namespace Terranet\Administrator\Services\Translations;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;

class Finder
{
    /**
     * Finds a translations that match $term.
     *
     * @param $translations
     * @param null $term
     * @param int $page
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function find($translations, $term = null, $page = 1, $perPage = 20)
    {
        $keys = [];
        foreach ($translations as $key => $translation) {
            foreach ($translation as $lang => $value) {
                if (empty($term) || Str::contains(strtoupper($value), strtoupper($term))
                    || Str::contains(strtoupper($key), strtoupper($term))) {
                    $keys[$key] = $translation;

                    continue;
                }
            }
        }

        return $this->paginate($keys, $page, $perPage);
    }

    /**
     * Builds a Paginator instance.
     *
     * @param $keys
     * @param $page
     * @param $perPage
     *
     * @return LengthAwarePaginator
     */
    protected function paginate($keys, $page, $perPage)
    {
        return new LengthAwarePaginator(
            $this->chunk($keys, $page, $perPage),
            \count($keys),
            20,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );
    }

    /**
     * Retrieves the current page chunk.
     *
     * @param $translations
     * @param int $page
     * @param int $perPage
     *
     * @return array
     */
    protected function chunk($translations, $page = 1, $perPage = 20)
    {
        if (!$count = \count($translations)) {
            return [];
        }

        $pages = array_chunk($translations, $perPage, true);

        $min = 1;
        $max = ceil($count / $perPage);

        if ($page < $min || $page > $max) {
            $page = 1;
        }

        return $pages[$page - 1];
    }
}
