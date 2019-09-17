<?php

namespace Terranet\Administrator\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Terranet\Administrator\AdminRequest;

class BatchController extends AdminController
{
    /**
     * Perform a batch action against selected collection.
     *
     * @param  AdminRequest  $request
     * @param  string  $page
     * @return RedirectResponse
     */
    public function batch(AdminRequest $request, string $page)
    {
        $resource = $request->resource();

        $this->authorize($action = $request->get('batch_action'), $model = $resource->model());

        $response = $resource->actions()->exec('batch::'.$action, [$model, $request]);

        if ($response instanceof Response || $response instanceof Renderable) {
            return $response;
        }

        return back()->with(
            'messages',
            [trans('administrator::messages.action_success')]
        );
    }

    /**
     * Export collection.
     *
     * @param  AdminRequest  $request
     * @param  string  $page
     * @param  string  $format
     * @return mixed
     * @throws \Exception
     */
    public function export(AdminRequest $request, string $page, string $format)
    {
        $resource = $request->resource();

        $this->authorize('index', $resource->model());

        $query = $resource->finder()->getQuery();

        return $resource->actions()->exec('export', [$query, $format]);
    }
}
