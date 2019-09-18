<?php

namespace Terranet\Administrator\Controllers;

use Illuminate\Support\Arr;
use Terranet\Administrator\AdminRequest;
use Terranet\Administrator\Architect;
use Terranet\Administrator\Requests\UpdateRequest;

class SettingsController extends AdminController
{
    /**
     * List settings by selected group [according to settings page name].
     *
     * @return $this
     */
    public function edit(AdminRequest $request)
    {
        $resource = $request->resource();
        $this->authorize('index', $eloquent = $resource->model());

        return view(Architect::template()->layout('settings'), [
            'settings' => options_fetch(),
            'resource' => $resource,
        ]);
    }

    /**
     * Save settings per page.
     *
     * @param  UpdateRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AdminRequest $request)
    {
        $resource = $request->resource();
        $this->authorize('update', $eloquent = $resource->model());

        options_save(Arr::except(
            $request->all(),
            ['_token', 'save']
        ));

        return back()->with('messages', ['Settings saved successfully']);
    }

    /**
     * @param  AdminRequest  $request
     * @return $this
     */
    public function index(AdminRequest $request)
    {
        $resource = $request->resource();
        $this->authorize('index', $eloquent = $resource->model());

        return $this->edit();
    }
}
