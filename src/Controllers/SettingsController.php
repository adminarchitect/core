<?php

namespace Terranet\Administrator\Controllers;

use Terranet\Administrator\Requests\UpdateRequest;

class SettingsController extends AdminController
{
    /**
     * List settings by selected group [according to settings page name]
     *
     * @return $this
     */
    public function edit()
    {
        $this->authorize('index', $eloquent = app('scaffold.model'));

        return view(app('scaffold.template')->layout('settings'), [
            'settings' => options_fetch()
        ]);
    }

    /**
     * Save settings per page
     *
     * @param UpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request)
    {
        $this->authorize('update', $eloquent = app('scaffold.model'));

        options_save(array_except(
            $request->all(),
            ['_token', 'save']
        ));

        return back()->with('messages', ['Settings saved successfully']);
    }

    public function index()
    {
        $this->authorize('index', $eloquent = app('scaffold.model'));

        return $this->edit();
    }
}
