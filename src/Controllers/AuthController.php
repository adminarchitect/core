<?php

namespace Terranet\Administrator\Controllers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\View;
use Terranet\Administrator\Architect;
use Illuminate\Support\Facades\Redirect;
use Terranet\Administrator\Requests\LoginRequest;
use Illuminate\Contracts\View\View as ViewContract;
use App\Http\Controllers\Controller as BaseController;

class AuthController extends BaseController
{
    /**
     * @param  LoginRequest  $request
     * @return RedirectResponse
     */
    public function postLogin(LoginRequest $request)
    {
        $config = app('scaffold.config');

        // basic login policy
        $credentials = $request->only(
            [
                $config->get('auth.identity', 'username'),
                $config->get('auth.credential', 'password'),
            ]
        );

        // extend auth policy by allowing custom login conditions
        if ($conditions = $config->get('auth.conditions', [])) {
            $credentials = array_merge($credentials, $conditions);
        }

        $remember = (int) $request->get('remember_me', 0);

        if ($this->guard()->attempt($credentials, $remember, true)) {
            if (\is_callable($url = $config->get('home_page'))) {
                $url = \call_user_func($url);
            }

            return Redirect::to(URL::to($url));
        }

        return Redirect::back()->withErrors([trans('administrator::errors.login_failed')]);
    }

    /**
     * @return ViewContract
     */
    public function getLogin(): ViewContract
    {
        return View::make(
            Architect::template()->auth('login')
        );
    }

    /**
     * @return RedirectResponse
     */
    public function getLogout()
    {
        $this->guard()->logout();

        return Redirect::to(
            URL::route('scaffold.login')
        );
    }

    /**
     * @return mixed
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }
}
