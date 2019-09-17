<?php

namespace Terranet\Administrator\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Symfony\Component\HttpFoundation\Response;
use Terranet\Administrator\AdminRequest;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Exception;
use Terranet\Administrator\Requests\UpdateRequest;
use Terranet\Administrator\Scaffolding;
use Terranet\Administrator\Services\MediaLibraryProvider;

class ScaffoldController extends AdminController
{
    /**
     * @param  AdminRequest  $request
     * @param  string  $page
     * @return View
     * @throws \Exception
     */
    public function index(AdminRequest $request, string $page)
    {
        /** @var Scaffolding $resource */
        $resource = $request->resource();

        $this->authorize('index', $resource->model());

        $items = $resource->finder()->fetchAll();

        return view($resource->template()->index('index'), [
            'items' => $items,
            'resource' => $resource,
            'filter' => $resource->filter(),
        ]);
    }

    /**
     * View resource.
     *
     * @param  AdminRequest  $request
     * @param  string  $page
     * @param  int  $id
     * @return View
     * @throws Exception
     */
    public function view(AdminRequest $request, string $page, $id)
    {
        /** @var Scaffolding $resource */
        $resource = $request->resource();

        $this->authorize('view', $eloquent = $request->resolveModel($id));

        return view($request->resource()->template()->view('index'), [
            'item' => $eloquent,
            'resource' => $resource,
        ]);
    }

    /**
     * Edit resource.
     *
     * @param  AdminRequest  $request
     * @param  string  $page
     * @param $id
     * @return View
     * @throws Exception
     */
    public function edit(AdminRequest $request, string $page, $id): View
    {
        /** @var Scaffolding $resource */
        $resource = $request->resource();

        $this->authorize('update', $eloquent = $request->resolveModel($id));

        return view($resource->template()->edit('index'), [
            'item' => $eloquent,
            'resource' => $resource,
        ]);
    }

    /**
     * @param                    $page
     * @param                    $id
     * @param  null|UpdateRequest  $request
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, string $page, $id)
    {
        /** @var Scaffolding $resource */
        $resource = $request->resource();

        $this->authorize('update', $eloquent = $request->resolveModel($id));

        try {
            $resource->actions()->exec('save', [$eloquent, $request]);
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
     * @param  AdminRequest  $request
     * @return View
     * @throws \Exception
     */
    public function create(AdminRequest $request)
    {
        /** @var Scaffolding $resource */
        $resource = $request->resource();

        $this->authorize('create', $eloquent = $resource->model());

        return view($request->resource()->template()->edit('index'), [
            'item' => $eloquent,
            'resource' => $resource,
        ]);
    }

    /**
     * Store new item.
     *
     * @param  UpdateRequest  $request
     * @param  string  $page
     * @return RedirectResponse
     * @throws \Exception
     */
    public function store(UpdateRequest $request, string $page)
    {
        /** @var Scaffolding $resource */
        $resource = $request->resource();

        $this->authorize('create', $eloquent = $resource->model());

        try {
            $eloquent = $resource->actions()->exec('save', [$eloquent, $request]);
        } catch (\Exception $e) {
            return back()->withErrors([$e->getMessage()]);
        }

        return $this->redirectTo($page, $eloquent->id, $request)->with('messages', [$this->translatedMessage('create_success', $resource)]);
    }

    /**
     * Destroy item.
     *
     * @param  AdminRequest  $request
     * @param  string  $page
     * @param $id
     * @return RedirectResponse
     */
    public function delete(AdminRequest $request, string $page, $id)
    {
        /** @var Scaffolding $module */
        $module = $request->resource();

        $this->authorize('delete', $eloquent = $request->resolveModel($id));

        try {
            $module->actions()->exec('delete', [$eloquent]);
        } catch (\Exception $e) {
            return back()->withErrors([$e->getMessage()]);
        }

        $message = $this->translatedMessage('remove_success', $module);

        return redirect()->to(route('scaffold.index', ['module' => $module]))->with('messages', [$message]);
    }

    /**
     * Destroy attachment.
     *
     * @param  AdminRequest  $request
     * @param  string  $page
     * @param $id
     * @param $attachment
     * @return RedirectResponse
     */
    public function deleteAttachment(AdminRequest $request, string $page, $id, $attachment)
    {
        /** @var Scaffolding $resource */
        $resource = $request->resource();

        $this->authorize('update', $eloquent = $request->resolveModel($id));

        try {
            $resource->actions()->exec('detachFile', [$eloquent, $attachment]);
        } catch (\Exception $e) {
            return back()->withErrors([$e->getMessage()]);
        }

        return back()->with('messages', [$this->translatedMessage('attachment_remove_success', $resource)]);
    }

    /**
     * Fetch media collection.
     *
     * @param  AdminRequest  $request
     * @param  string  $page
     * @param $id
     * @return JsonResponse
     */
    public function fetchMedia(AdminRequest $request, string $page, $id)
    {
        /** @var HasMedia $eloquent */
        $this->authorize('view', $eloquent = $request->resolveModel($id));

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
     * @param  AdminRequest  $request
     * @param  string  $page
     * @param $id
     * @param  string  $collection
     * @return RedirectResponse
     */
    public function attachMedia(AdminRequest $request, string $page, $id, string $collection)
    {
        /** @var HasMedia $eloquent */
        $this->authorize('update', $eloquent = $request->resolveModel($id));

        $file = $request->file('_media_')[$collection];

        $mediaItem = MediaLibraryProvider::forModel($eloquent)->attach($file, $collection);

        return response()->json(MediaLibraryProvider::toJson($mediaItem));
    }

    /**
     * Detach a media file from resource.
     *
     * @param  AdminRequest  $request
     * @param  string  $page
     * @param $id
     * @param $mediaId
     * @return JsonResponse
     */
    public function detachMedia(AdminRequest $request, string $page, $id, $mediaId)
    {
        /** @var HasMedia $eloquent */
        $this->authorize('update', $eloquent = $request->resolveModel($id));

        MediaLibraryProvider::forModel($eloquent)->detach($mediaId);

        return response()->json([], \Illuminate\Http\Response::HTTP_NO_CONTENT);
    }

    /**
     * Search for a model(s).
     *
     * @param  AdminRequest  $request
     * @return JsonResponse
     */
    public function search(AdminRequest $request): JsonResponse
    {
        $term = $request->get('query');
        $eloquent = $request->get('searchable');

        $items = [];

        if ($eloquent) {
            $column = $request->get('field');
            /** @var Model $eloquent */
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
     * @param  AdminRequest  $request
     * @param  string  $page
     * @param $id
     * @param  string  $action
     * @return RedirectResponse
     */
    public function action(AdminRequest $request, string $page, $id, string $action)
    {
        /** @var Module $resource */
        $resource = $request->resource();

        $this->authorize($action, $eloquent = $request->resolveModel($id));

        $response = $resource->actions()->exec('action::'.$action, [$eloquent]);

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

        return $this->translator()->has($key = sprintf('administrator::messages.%s.%s', $resource->url(), $action))
            ? trans($key)
            : trans(sprintf('administrator::messages.%s', $action));
    }

    /**
     * @param  string  $term
     * @param  Model  $eloquent
     * @param  string  $column
     * @return mixed
     */
    protected function searchableQuery(string $term, Model $eloquent, string $column): Builder
    {
        $searchByKey = is_numeric($term);
        $searchableKey = $searchByKey ? $eloquent->getKeyName() : $column;

        return $eloquent->newQuery()
            ->when($searchByKey, function (Builder $query) use ($searchableKey, $term) {
                return $query->where($searchableKey, (int) $term);
            })
            ->when(!$searchByKey, function (Builder $query) use ($searchableKey, $term) {
                return $query->orWhere($searchableKey, 'LIKE', "%{$term}%");
            });
    }
}
