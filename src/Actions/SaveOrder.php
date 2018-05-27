<?php

namespace Terranet\Administrator\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Terranet\Administrator\Traits\Actions\BatchSkeleton;
use Terranet\Administrator\Traits\Actions\Skeleton;

class SaveOrder
{
    use Skeleton, BatchSkeleton;

    /**
     * Batch update elements ranking value.
     *
     * @param Model $eloquent
     * @param Request $request
     *
     * @return mixed
     */
    public function handle(Model $eloquent, Request $request)
    {
        return $eloquent->syncRanking($request->get($eloquent->getRankableColumn(), []));
    }

    protected function icon()
    {
        return 'fa-sort';
    }
}
