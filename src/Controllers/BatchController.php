<?php

namespace Terranet\Administrator\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Terranet\Administrator\Scaffolding;

class BatchController extends AdminController
{
    /**
     * Perform a batch action against selected collection.
     *
     * @param         $page
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function batch($page, Request $request)
    {
        $resource = app('scaffold.module');

        $this->authorize($action = $request->get('batch_action'), $model = $resource->model());

        $response = $resource->actionsManager()->exec('batch::'.$action, [$model, $request]);

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
     * @param $page
     * @param $format
     *
     * @return mixed
     */
    public function export($page, $format)
    {
        /** @var Scaffolding $resource */
        $resource = app('scaffold.module');

        $this->authorize('index', $resource->model());

        $query = $resource->finder()->getQuery();

        return $resource->scaffoldActions()->exec('export', [$query, $format]);
    }
}
