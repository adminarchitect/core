<?php

namespace Terranet\Administrator\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Requests\UpdateRequest;
use Terranet\Administrator\Scaffolding;
use Terranet\Administrator\Services\MediaLibraryProvider;

class ScaffoldController extends AdminController
{
    /**
     * @param        $page
     * @param  Module  $resource
     * @return View
     */
    public function index($page, Module $resource)
    {
        $this->authorize('index', $resource->model());

        $items = $resource->finderInstance()->fetchAll();

        return view(app('scaffold.template')->index('index'), ['items' => $items]);
    }

    /**
     * View resource.
     *
     * @param $page
     * @param $id
     * @return View
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
     * @return View
     */
    public function edit($page, $id): View
    {
        $this->authorize('update', $eloquent = app('scaffold.model'));

        return view(app('scaffold.template')->edit('index'), [
            'item' => $eloquent,
        ]);
    }

    /**
     * @param                    $page
     * @param                    $id
     * @param  null|UpdateRequest  $request
     * @return RedirectResponse
     */
    public function update($page, $id, UpdateRequest $request)
    {
        /** @var Scaffolding $resource */
        $resource = app('scaffold.module');

        $this->authorize('update', $eloquent = app('scaffold.model'));

        try {
            $resource->actionsManager()->exec('save', [$eloquent, $request]);
        } catch (\Exception $e) {
            return back()->withErrors([$e->getMessage()]);
        }

        return $this->redirectTo($page, $id, $request)->with(
            'messages',
            [$this->translatedMessage('update_success', $resource)]
        );
    }

    /**
     * Create new item.
     *
     * @return View
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
     * @param  null|UpdateRequest  $request
     * @return RedirectResponse
     */
    public function store($page, UpdateRequest $request)
    {
        /** @var Scaffolding $resource */
        $resource = app('scaffold.module');

        $this->authorize('create', $eloquent = $resource->model());

        try {
            $eloquent = $resource->actionsManager()->exec('save', [$eloquent, $request]);
        } catch (\Exception $e) {
            return back()->withErrors([$e->getMessage()]);
        }

        return $this->redirectTo($page, $eloquent->id, $request)->with('messages', [$this->translatedMessage('create_success', $resource)]);
    }

    /**
     * Destroy item.
     *
     * @param  Module  $module
     * @return RedirectResponse
     */
    public function delete(Module $module)
    {
        $this->authorize('delete', $eloquent = app('scaffold.model'));

        try {
            $module->actionsManager()->exec('delete', [$eloquent]);
        } catch (\Exception $e) {
            return back()->withErrors([$e->getMessage()]);
        }

        $message = $this->translatedMessage('remove_success', $module);

        return redirect()->to(route('scaffold.index', ['module' => $module]))->with('messages', [$message]);
    }

    /**
     * Destroy attachment.
     *
     * @param $page
     * @param $id
     * @param $attachment
     * @return RedirectResponse
     */
    public function deleteAttachment($page, $id, $attachment)
    {
        /** @var Module $resource */
        $resource = app('scaffold.module');

        $this->authorize('update', $eloquent = app('scaffold.model'));

        try {
            $resource->actionsManager()->exec('detachFile', [$eloquent, $attachment]);
        } catch (\Exception $e) {
            return back()->withErrors([$e->getMessage()]);
        }

        return back()->with('messages', [$this->translatedMessage('attachment_remove_success', $resource)]);
    }

    /**
     * @param $module
     * @param $id
     * @param  Request  $request
     * @return JsonResponse
     */
    public function fetchMedia($module, $id, Request $request)
    {
        $this->authorize('view', $eloquent = app('scaffold.model'));

        $media = MediaLibraryProvider::forModel($eloquent)->fetch(
            $request->get('collection', 'default'),
            20
        );

        $items = collect($media->items())->map([MediaLibraryProvider::class, 'toJson']);

        return response()->json(array_merge(
            $media->toArray(),
            ['data' => $items->toArray()]
        ));
    }

    /**
     * @param $page
     * @param $id
     * @param  string  $collection
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function attachMedia($page, $id, string $collection, Request $request)
    {
        $this->authorize('update', $eloquent = app('scaffold.model'));

        $file = $request->file('_media_')[$collection];

        $mediaItem = MediaLibraryProvider::forModel($eloquent)->attach($file, $collection);

        return response()->json(MediaLibraryProvider::toJson($mediaItem));
    }

    /**
     * @param $page
     * @param $id
     * @param $mediaId
     * @return JsonResponse
     */
    public function detachMedia($page, $id, $mediaId)
    {
        $this->authorize('update', $eloquent = app('scaffold.model'));

        MediaLibraryProvider::forModel($eloquent)->detach($mediaId);

        return response()->json([], \Illuminate\Http\Response::HTTP_NO_CONTENT);
    }

    /**
     * Search for a model(s).
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $term = $request->get('query');
        $eloquent = $request->get('searchable');

        $items = [];

        if ($eloquent) {
            $column = $request->get('field');
            $eloquent = new $eloquent();

            $query = method_exists($eloquent, 'searchableQuery')
                ? $eloquent->searchableQuery($term)
                : $this->searchableQuery($term, $eloquent, $column);

            $items = $query
                ->get(["{$eloquent->getKeyName()} as id", "{$column} as name"])
                ->toArray();
        }

        return response()->json(['items' => $items]);
    }

    /**
     * Custom action related to item.
     *
     * @param $page
     * @param $id
     * @param $action
     * @return RedirectResponse
     */
    public function action($page, $id, $action)
    {
        /** @var Module $resource */
        $resource = app('scaffold.module');

        $this->authorize($action, $eloquent = app('scaffold.model'));

        $response = $resource->actionsManager()->exec('action::'.$action, [$eloquent]);

        if ($response instanceof Response || $response instanceof Renderable) {
            return $response;
        }

        return back()->with('messages', [$this->translatedMessage('action_success', $resource)]);
    }

    /**
     * Generate action message.
     *
     * @param  string  $action
     * @param  Module  $resource
     * @return string
     */
    protected function translatedMessage(string $action, $resource): string
    {
        if (method_exists($resource, 'flashMessage')) {
            return $resource->flashMessage(request(), $action);
        }

        return $this->translator->has($key = sprintf('administrator::messages.%s.%s', $resource->url(), $action))
            ? trans($key)
            : trans(sprintf('administrator::messages.%s', $action));
    }

    /**
     * @param $term
     * @param $eloquent
     * @param $column
     * @return mixed
     */
    protected function searchableQuery($term, $eloquent, $column): Builder
    {
        $searchByKey = is_numeric($term);
        $searchableKey = $searchByKey ? $eloquent->getKeyName() : $column;

        return $eloquent->newQuery()
            ->when($searchByKey, function ($query) use ($searchableKey, $term) {
                return $query->where($searchableKey, (int) $term);
            })
            ->when(!$searchByKey, function ($query) use ($searchableKey, $term) {
                return $query->orWhere($searchableKey, 'LIKE', "%{$term}%");
            });
    }
}
