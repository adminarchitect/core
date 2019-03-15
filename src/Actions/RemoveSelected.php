<?php

namespace Terranet\Administrator\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Traits\Actions\BatchSkeleton;
use Terranet\Administrator\Traits\Actions\Skeleton;

class RemoveSelected
{
    use Skeleton, BatchSkeleton;

    /**
     * Delete collection elements.
     *
     * @param Model $eloquent
     * @param Request $request
     *
     * @return mixed
     */
    public function handle(Model $eloquent, Request $request)
    {
        return $this->fetchForDelete($eloquent, $request)
            ->each(function ($item) {
                return $this->canDelete($item) ? $item->delete() : $item;
            });
    }

    /**
     * Check if deletion of each item is authorized.
     *
     * @param Model $eloquent
     *
     * @return bool
     */
    protected function canDelete(Model $eloquent)
    {
        /** @var Module $resource */
        $resource = app('scaffold.module');

        return $resource->actionsManager()->authorize('delete', $eloquent);
    }

    /**
     * @param Model $eloquent
     * @param Request $request
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model[]
     */
    protected function fetchForDelete(Model $eloquent, Request $request)
    {
        return $eloquent->newQueryWithoutScopes()
            ->whereIn('id', $request->get('collection', []))
            ->get();
    }

    /**
     * @return string
     */
    protected function icon()
    {
        return 'fa-trash';
    }
}
