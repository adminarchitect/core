<?php

namespace Terranet\Administrator\Traits;

use DOMDocument;
use Illuminate\Database\Eloquent\Builder;
use Response;
use Terranet\Administrator\Exception;

trait ExportsCollection
{
    protected function exportColumns()
    {
        return ['*'];
    }

    /**
     * Export collection to a specific format
     *
     * @param Builder $query
     * @param         $format
     * @return mixed
     * @throws Exception
     */
    public function export(Builder $query, $format)
    {
        $method = "to" . strtoupper($format);

        if (!method_exists($this, $method)) {
            throw new Exception(sprintf('Don\'t know how to export to %s format', $format));
        }

        return call_user_func_array([$this, $method], [$query]);
    }

    /**
     * Convert & download collection in JSON format
     *
     * @param $query
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function toJSON(Builder $query)
    {
        file_put_contents(
            $file = $this->getFilename(),
            json_encode($query->select($this->exportColumns())->get())
        );

        return $this->sendDownloadResponse($file, 'json', ['Content-Type' => 'application/json']);
    }

    /**
     * Convert & download collection in XML format
     *
     * @param $query
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function toXML(Builder $query)
    {
        $dom = new DOMDocument();
        $root = $dom->createElement('root');

        $query->select($this->exportColumns())->chunk(100, function ($collection) use ($dom, $root) {
            foreach ($collection as $object) {
                $item = $dom->createElement('item');

                foreach ($this->toScalar($object) as $column => $value) {
                    $column = $dom->createElement($column, htmlspecialchars($value));
                    $item->appendChild($column);
                }

                $root->appendChild($item);
            }
        });
        $dom->appendChild($root);

        file_put_contents(
            $file = $this->getFilename(),
            $dom->saveXML()
        );

        return $this->sendDownloadResponse($file, 'xml', ['Content-Type' => 'text/xml']);
    }

    /**
     * Convert & download collection in CSV format
     *
     * @param $query
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function toCSV(Builder $query)
    {
        $out = fopen(
            $file = $this->getFilename(),
            "a+"
        );

        $headersPrinted = false;
        $query->select($this->exportColumns())->chunk(100, function ($collection) use ($out, &$headersPrinted) {
            foreach ($collection as $item) {
                $data = $this->toScalar($item);

                if (!$headersPrinted) {
                    fputcsv($out, array_keys($data));
                    $headersPrinted = true;
                }

                fputcsv($out, $data);
            }
        });
        fclose($out);

        return $this->sendDownloadResponse($file, 'csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * @param $object
     * @return array|ø
     */
    protected function toScalar($object)
    {
        return array_filter($object->toArray(), function ($item) {
            return is_scalar($item);
        });
    }

    /**
     * @return string
     */
    protected function getFilename()
    {
        $file = tempnam(sys_get_temp_dir(), app('scaffold.module')->url() . "_");

        return $file;
    }

    /**
     * @param $file
     * @return mixed
     */
    protected function sendDownloadResponse($file, $extension, array $headers = [])
    {
        return response()->download(
            $file,
            app('scaffold.module')->title() . '.' . $extension,
            $headers
        );
    }
}
