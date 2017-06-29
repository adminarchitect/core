<?php

namespace Terranet\Administrator\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $this->authorize($action = $request->get('batch_action'), $model = app('scaffold.module')->model());

        $this->rememberPreviousPage();

        $response = app('scaffold.actions')->exec('batch::' . $action, [$model, $request]);

        if ($response instanceof Response || $response instanceof Renderable) {
            return $response;
        }

        return redirect()->to($this->getPreviousUrl())->with(
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
        $this->authorize('index', app('scaffold.module')->model());

        $this->rememberPreviousPage();

        $query = app('scaffold.finder')->getQuery();

        return app('scaffold.actions')->exec('export', [$query, $format]);
    }
}
