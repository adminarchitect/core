<?php

namespace Terranet\Administrator\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Translation\Translator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Terranet\Administrator\Contracts\Module;
use Terranet\Administrator\Middleware\Authenticate;
use Terranet\Administrator\Middleware\AuthProvider;
use Terranet\Administrator\Middleware\Resources;

abstract class AdminController extends BaseController
{
    public function __construct()
    {
        $this->middleware([
            AuthProvider::class,
            Authenticate::class,
            Resources::class,
        ]);
    }

    /**
     * Authorize a given action against a set of arguments.
     *
     * @param  mixed  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function authorize($ability, $arguments = null)
    {
        /** @var Module $resource */
        $resource = app('scaffold.module');

        if (!$response = $resource->actions()->authorize($ability, $arguments)) {
            throw $this->createGateUnauthorizedException(
                $ability,
                trans('administrator::errors.unauthorized')
            );
        }

        return $response;
    }

    protected function redirectTo($module, $key, Request $request)
    {
        if ($next = $request->get('back_to')) {
            return redirect()->to($next);
        }

        if ($request->exists('save')) {
            return redirect()->route('scaffold.edit', ['module' => $module, 'id' => $key]);
        }

        return redirect()->route(
            $request->exists('save_return') ? 'scaffold.index' : 'scaffold.create',
            ['module' => $module]
        );
    }

    /**
     * Throw an unauthorized exception based on gate results.
     *
     * @param  string  $ability
     * @param  string  $message
     * @param  \Exception  $previousException
     * @return \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function createGateUnauthorizedException(
        $ability,
        $message = 'This action is unauthorized.',
        $previousException = null
    ) {
        $message = sprintf($message.' [%s]', $ability);

        return new HttpException(403, $message, $previousException);
    }

    public function translator()
    {
        return app(Translator::class);
    }
}
