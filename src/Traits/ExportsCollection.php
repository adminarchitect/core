<?php

namespace Terranet\Administrator\Traits;

use Carbon\Carbon;
use DOMDocument;
use Generator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Terranet\Administrator\Exception;
use Terranet\Translatable\Translatable;

trait ExportsCollection
{
    /**
     * Fetch exportable items by query.
     *
     * @param Builder $query
     * @return Builder
     */
    protected function exportableQuery(Builder $query): Builder
    {
        # Allow executing custom exportable query.
        if (method_exists($this->module, 'exportableQuery')) {
            return $this->module->exportableQuery($query);
        }

        return $query
            ->when($query->getModel() instanceof Translatable, function ($query) {
                $query->translated();
            })
            # leave select after joining with translations
            # table in order to rewrite selected columns
            ->select($this->exportableColumns());
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
            json_encode($this->exportableQuery($query)->get())
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
        $root = with($dom = new DOMDocument)->createElement('root');

        foreach ($this->each($query) as $object) {
            $item = $dom->createElement('item');

            foreach ($this->toScalar($object) as $column => $value) {
                $column = $dom->createElement($column, htmlspecialchars($value));
                $item->appendChild($column);
            }

            $root->appendChild($item);
        }

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

        foreach ($this->each($query, 100) as $item) {
            $data = $this->toScalar($item);

            if (!$headersPrinted) {
                fputcsv($out, array_keys($data));
                $headersPrinted = true;
            }

            fputcsv($out, $data);
        }
        fclose($out);

        return $this->sendDownloadResponse($file, 'csv', ['Content-Type' => 'text/csv']);
    }

    public function toPDF(Builder $query)
    {
        if (!app()->has('dompdf.wrapper')) {
            throw new Exception(sprintf(
                "'%s' package required to generate PDF documents.",
                'barryvdh/laravel-dompdf'
            ));
        }

        $pdf = app('dompdf.wrapper');
        $view = method_exists($this->module, 'exportableView')
            ? $this->module->exportableView()
            : $this->exportableView();
        
        $html = view($view, [
            'module' => app('scaffold.module')->url(),
            'time' => new Carbon(),
            'items' => $this->each($query, 100),
        ])->render();

        return $pdf->loadHTML($html)
                   ->setPaper('a4', 'landscape')
                   ->download(app('scaffold.module')->url() . '.pdf');
    }

    /**
     * @param $object
     * @return array
     */
    protected function toScalar($object): array
    {
        return array_filter($object->toArray(), function ($item) {
            return is_scalar($item);
        });
    }

    /**
     * @return string
     */
    protected function getFilename(): string
    {
        return tempnam(sys_get_temp_dir(), app('scaffold.module')->url() . "_");
    }

    /**
     * @param $file
     * @param $extension
     * @param array $headers
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

    /**
     * Creates a all items generator.
     *
     * @param Builder $query
     * @param int $count
     * @return Generator
     */
    protected function each(Builder $query, $count = 100): Generator
    {
        $query = $this->exportableQuery($query);

        # enforce order by statement.
        if (empty($query->orders) && empty($query->unionOrders)) {
            $query->orderBy($query->getModel()->getQualifiedKeyName(), 'asc');
        }

        $page = 1;

        do {
            // We'll execute the query for the given page and get the results. If there are
            // no results we can just break and return from here. When there are results
            // we will call the callback with the current chunk of these results here.
            $results = $query->forPage($page, $count)->get();

            $countResults = $results->count();

            if ($countResults == 0) {
                break;
            }

            // On each chunk result set, we will pass them to the callback and then let the
            // developer take care of everything within the callback, which allows us to
            // keep the memory low for spinning through large result sets for working.
            foreach ($results as $index => $item) {
                yield $index = $item;
            }

            unset($results);

            $page++;
        } while ($countResults == $count);
    }

    /**
     * Generate a list of exportable columns.
     *
     * @return array
     */
    protected function exportableColumns(): array
    {
        if (method_exists($this->module, 'exportableColumns')) {
            return $this->module->exportableColumns();
        }

        /**
         * @var Model $model
         */
        $model = $this->module->model();

        return collect($model->getFillable())
            ->prepend('id')
            ->diff($model->getHidden())
            ->map(function ($column) use ($model) {
                return "{$model->getTable()}.{$column}";
            })
            ->when($model instanceof Translatable, function (Collection $collection) use ($model) {
                return $collection->merge(
                    collect($model->getTranslatedAttributes())
                        ->map(function ($column) use ($model) {
                            return "tt.{$column}";
                        })
                );
            })
            ->all();
    }
    
    protected function exportableView()
    {
        return 'administrator::layouts.exportable';
    }
}
