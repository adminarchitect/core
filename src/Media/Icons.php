<?php

namespace Terranet\Administrator\Media;

use Illuminate\Support\Arr;

class Icons
{
    protected static $table = [
        'Dir' => 'fa fa-folder-open-o',
        'Pdf' => 'fa fa-file-pdf-o',
        'Code' => 'fa fa-file-code-o',
        'Excel' => 'fa fa-file-excel-o',
        'Word' => 'fa fa-file-word-o',
        'PowerPoint' => 'fa fa-file-powerpoint-o',
        'Archive' => 'fa fa-file-archive-o',
        'Image' => 'fa fa-file-image-o',
        'Audio' => 'fa fa-file-audio-o',
        'Movie' => 'fa fa-file-video-o',
    ];

    public function table()
    {
        return array_keys(static::$table);
    }

    public function icon($group)
    {
        return Arr::get(static::$table, $group, 'fa fa-file-text-o');
    }
}
