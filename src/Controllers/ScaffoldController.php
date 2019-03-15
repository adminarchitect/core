<?php

namespace Terranet\Administrator\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Requests\UpdateRequest;
use Illuminate\Support\Facades\URL;

class ScaffoldController extends AdminController
{
    /**
     * @param        $page
     * @param Module $resource
     *
     * @return \Illuminate\View\View
     */
    public function index($page, Module $resource)
    {
        $this->authorize('index', $resource->model());

        $items = app('scaffold.finder')->fetchAll();

        return view(app('scaffold.template')->index('index'), ['items' => $items]);
    }

    /**
     * View resource.
     *
     * @param $page
     * @param $id
     *
     * @return \Illuminate\View\View
     */
    public function view($page, $id)
    {
        $this->authorize('view', $eloquent = app('scaffold.model'));

        return view(app('scaffold.template')->view('index'), [
            'item' => $eloquent,
        ]);
    }

    /**
     * Edit resource.
     *
     * @param $page
     * @param $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($page, $id)
    {
        $this->authorize('update', $eloquent = app('scaffold.model'));

        return view(app('scaffold.template')->edit('index'), [
            'item' => $eloquent,
        ]);
    }

    /**
     * @param                    $page
     * @param                    $id
     * @param null|UpdateRequest $request
     *
     * @return RedirectResponse
     */
    public function update($page, $id, UpdateRequest $request)
    {
        $this->authorize('update', $eloquent = app('scaffold.model'));

        try {
            app('scaffold.actions')->exec('save', [$eloquent, $request]);
        } catch (\Exception $e) {
            return back()->withErrors([$e->getMessage()]);
        }

        return $this->redirectTo($page, $id, $request)->with('messages', [trans('administrator::messages.update_success')]);
    }

    /**
     * Create new item.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', $eloquent = app('scaffold.module')->model());

        return view(app('scaffold.template')->edit('index'), ['item' => $eloquent]);
    }

    /**
     * Store new item.
     *
     * @param                    $page
     * @param null|UpdateRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($page, UpdateRequest $request)
    {
        $this->authorize('create', $eloquent = app('scaffold.module')->model());

        try {
            $eloquent = app('scaffold.actions')->exec('save', [$eloquent, $request]);
        } catch (\Exception $e) {
            return back()->withErrors([$e->getMessage()]);
        }

        return $this->redirectTo($page, $eloquent->id, $request)->with(
            'messages',
            [trans('administrator::messages.create_success')]
        );
    }

    /**
     * Destroy item.
     *
     * @param Module $module
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Module $module)
    {
        $this->authorize('delete', $eloquent = app('scaffold.model'));

        $id = $eloquent->id;

        app('scaffold.actions')->exec('delete', [$eloquent]);

        $message = trans('administrator::messages.remove_success');

        if (URL::previous() === route('scaffold.view', ['module' => $module, 'id' => $id])) {
            return back()->with('messages', [$message]);
        }

        return redirect()->to(route('scaffold.index', ['module' => $module]))->with('messages', [$message]);
    }

    /**
     * Destroy attachment.
     *
     * @param $page
     * @param $id
     * @param $attachment
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAttachment($page, $id, $attachment)
    {
        $this->authorize('update', $eloquent = app('scaffold.model'));

        app('scaffold.actions')->exec('detachFile', [$eloquent, $attachment]);

        return back()->with('messages', [trans('administrator::messages.remove_success')]);
    }

    /**
     * Search for a model(s).
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request): \Illuminate\Http\JsonResponse
    {
        $eloquent = $request->get('searchable');
        $column = $request->get('field');
        $term = $request->get('query');

        $items = [];

        if ($eloquent && $column) {
            $eloquent = new $eloquent();
            $searchByKey = is_numeric($term);
            $searchableKey = $searchByKey ? $eloquent->getKeyName() : $column;

            $instance = $eloquent
                ->when($searchByKey, function ($query) use ($searchableKey, $term) {
                    return $query->where($searchableKey, (int) $term);
                })
                ->when(!$searchByKey, function ($query) use ($searchableKey, $term) {
                    return $query->orWhere($searchableKey, 'LIKE', "%{$term}%");
                })
                ->get(["{$eloquent->getKeyName()} as id", "{$column} as name"]);

            $items = $instance->toArray();
        }

        return response()->json(['items' => $items]);
    }

    /**
     * Custom action related to item.
     *
     * @param $page
     * @param $id
     * @param $action
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function action($page, $id, $action)
    {
        $this->authorize($action, $eloquent = app('scaffold.model'));

        $response = app('scaffold.actions')->exec('action::'.$action, [$eloquent]);

        if ($response instanceof Response || $response instanceof Renderable) {
            return $response;
        }

        return back()->with(
            'messages',
            [trans('administrator::messages.action_success')]
        );
    }
}
