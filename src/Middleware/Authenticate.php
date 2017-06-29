<?php

namespace Terranet\Administrator\Middleware;

use Closure;
use Illuminate\Http\Request;
use Terranet\Administrator\PermissionChecker;

class Authenticate
{
    protected $settings;

    protected $loginUrl;

    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * Authenticate constructor.
     *
     * @param PermissionChecker $permissionChecker
     */
    public function __construct(PermissionChecker $permissionChecker)
    {
        $this->settings = app('scaffold.config');
        $this->loginUrl = route('scaffold.login');
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Check global permission
        $rule = $this->settings->get('permission');

        $response = $this->permissionChecker->isPermissionGranted(
            app($rule)->validate()
        );

        if (!$response) {
            auth('admin')->logout();

            return response()
                ->redirectGuest($this->loginUrl)
                ->with('redirect', $request->url());
        }

        if ($this->isResponseObject($response)) {
            return $response;
        }

        if ($this->redirectReceived($response)) {
            return $response->with('redirect', $request->url());
        }

        return $next($request);
    }

    /**
     * @param $response
     * @return bool
     */
    protected function isResponseObject($response)
    {
        return is_a($response, 'Illuminate\Http\JsonResponse') || is_a($response, 'Illuminate\Http\Response');
    }

    /**
     * @param $response
     * @return bool
     */
    protected function redirectReceived($response)
    {
        return is_a($response, 'Illuminate\\Http\\RedirectResponse');
    }
}
