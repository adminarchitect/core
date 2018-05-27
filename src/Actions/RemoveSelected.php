<?php

namespace Terranet\Administrator\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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
        return $eloquent->newQueryWithoutScopes()->whereIn('id', $request->get('collection', []))->get()->each(function ($item) {
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
        return app('scaffold.actions')->authorize('delete', $eloquent);
    }

    protected function icon()
    {
        return 'fa-trash';
    }
}
