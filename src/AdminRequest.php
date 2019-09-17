<?php

namespace Terranet\Administrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Contracts\Services\Finder;

class AdminRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }

    /**
     * Resolve resource based on router parameters.
     *
     * @return null|Module
     */
    public function resource(): ?Module
    {
        return once(function () {
            return tap(Architect::resourceForKey($this->route()->parameter('module')), function ($resource) {
                abort_if(is_null($resource), 404);
            });
        });
    }

    /**
     * Resolve current model.
     *
     * @param $id
     * @return null|Model
     */
    public function resolveModel($id): ?Model
    {
        return once(function () use ($id) {
            /** @var Finder $finder */
            $finder = $this->resource()->finder();

            if ($finder) {
                abort_unless($item = $finder->find($id), 404);
            }

            return $item ?? null;
        });
    }
}
