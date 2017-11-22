<?php

namespace Terranet\Administrator\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Terranet\Administrator\Middleware\Authenticate;
use Terranet\Administrator\Middleware\AuthProvider;
use Terranet\Administrator\Middleware\Badges;
use Terranet\Administrator\Middleware\Resources;

abstract class AdminController extends BaseController
{
    public function __construct()
    {
        if (!guarded_auth()) {
            $this->middleware(AuthProvider::class);
        }

        $this->middleware(Authenticate::class);

        $this->middleware(Resources::class);

        $this->middleware(Badges::class);
    }

    /**
     * Authorize a given action against a set of arguments.
     *
     * @param mixed $ability
     * @param mixed|array $arguments
     *
     * @return bool
     */
    public function authorize($ability, $arguments = [])
    {
        if (!$response = app('scaffold.actions')->authorize($ability, $arguments)) {
            throw $this->createGateUnauthorizedException(
                $ability,
                trans('administrator::errors.unauthorized')
            );
        }

        return $response;
    }

    protected function redirectTo($module, $key = null, Request $request)
    {
        if ($next = $request->get('back_to')) {
            return redirect()->to($next);
        }

        if ($request->exists('save')) {
            return redirect(route('scaffold.edit', $this->toMagnetParams(['module' => $module, 'id' => $key])));
        }

        return redirect(
            route(
                $request->exists('save_return') ? 'scaffold.index' : 'scaffold.create',
                $this->toMagnetParams(['module' => $module])
            )
        );
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function toMagnetParams(array $data = [])
    {
        return app('scaffold.magnet')->with($data)->toArray();
    }

    /**
     * Throw an unauthorized exception based on gate results.
     *
     * @param  string $ability
     * @param  string $message
     * @param  \Exception $previousException
     * @return \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function createGateUnauthorizedException(
        $ability,
        $message = 'This action is unauthorized.',
        $previousException = null
    ) {
        $message = sprintf($message . ' [%s]', $ability);

        return new HttpException(403, $message, $previousException);
    }
}
