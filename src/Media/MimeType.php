<?php

namespace Terranet\Administrator\Media;

use Illuminate\Support\Str;
use SplFileInfo;
use Terranet\Administrator\Exception;

class MimeType
{
    /**
     * @var SplFileInfo
     */
    protected $file;

    /**
     * MimeType constructor.
     *
     * @param $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    public function __call($method, $args)
    {
        if (method_exists($this->file, $method)) {
            return \call_user_func_array([$this->file, $method], $args);
        }

        throw new Exception("Method $method not found.");
    }

    public function is($type)
    {
        $method = 'is'.Str::studly($type);

        return $this->$method();
    }

    /**
     * @return bool
     */
    public function isImage()
    {
        return (bool) preg_match('~(png|jpe?g|gif|tiff|pje?pg|bmp)~si', $this->file->getExtension());
    }

    /**
     * @return bool
     */
    public function isAudio()
    {
        return (bool) preg_match('~(mp3|wave?|og[ga]+|flac|aac|3gp|m4a|wav|raw|wma)~si', $this->file->getExtension());
    }

    /**
     * @return bool
     */
    public function isMovie()
    {
        return (bool) preg_match('~(avi|mp4|mpe?g|mov|wmv|webm|mkv|flv|vob|og[vg]+|mov|3gp)~si', $this->file->getExtension());
    }

    /**
     * @return bool
     */
    public function isPdf()
    {
        return 'pdf' === $this->file->getExtension();
    }

    /**
     * @return bool
     */
    public function isExcel()
    {
        return (bool) preg_match('~(xls.?|numbers|csv)~si', $this->file->getExtension());
    }

    /**
     * @return bool
     */
    public function isWord()
    {
        return (bool) preg_match('~(docx?|pages)~si', $this->file->getExtension());
    }

    /**
     * @return bool
     */
    public function isPowerPoint()
    {
        return (bool) preg_match('~(ppt)~si', $this->file->getExtension());
    }

    public function isArchive()
    {
        return (bool) preg_match('~([bq]*zip2?|[tr]+ar|compress|7z|dmg|Cabinet|xz|tar\.gzip)~si', $this->file->getExtension());
    }

    public function isCode()
    {
        return (bool) preg_match('~(php|html?|css|js|cpp|sh)~si', $this->file->getExtension());
    }
}
