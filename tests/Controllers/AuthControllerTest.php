<?php

namespace Tests\Controllers;

use Illuminate\Auth\SessionGuard;
use Illuminate\Config\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use PHPUnit\Framework\MockObject\MockObject;
use Terranet\Administrator\Controllers\AuthController;
use Terranet\Administrator\Requests\LoginRequest;
use Terranet\Administrator\Services\Template;
use Terranet\Administrator\Tests\CoreTestCase;
use Terranet\Administrator\Tests\MocksObjects;

class AuthControllerTest extends CoreTestCase
{
    use MocksObjects;

    /** @var AuthController|MockObject */
    private $controller;

    /** @var MockObject|SessionGuard */
    private $guard;

    public function setUp(): void
    {
        $this->controller = $this->getMockBuilder(AuthController::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->onlyMethods(['guard'])
            ->getMock();

        $this->guard = $this->getMockBuilder(SessionGuard::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['attempt', 'logout'])
            ->getMock();

        $this->controller->method('guard')->willReturn($this->guard);

        parent::setUp();
    }

    /** @test */
    public function it_logs_out_the_user()
    {
        $this->guard->expects($this->once())->method('logout');

        URL::shouldReceive('route')->once()->with('scaffold.login')->andReturn('/login');
        Redirect::shouldReceive('to')->once()->with('/login');

        $this->controller->getLogout();
    }

    /** @test */
    public function it_logs_in_the_user()
    {
        $config = $this->createMock(Repository::class);
        $config->expects($this->exactly(4))
            ->method('get')
            ->willReturnCallback(fn($key, $default) => match ([$key, $default]) {
                ['auth.identity', 'username'] => 'username',
                ['auth.credential', 'password'] => 'password',
                ['auth.conditions', []] => ['active' => true],
                ['home_page', null] => function () {
                    return '/home';
                }
            });

        /** @var LoginRequest|MockObject $request */
        $request = $this->createMock(LoginRequest::class);
        $credentials = [
            'username' => 'admin@example.com',
            'password' => 'secret',
        ];

        $request->expects($this->once())
            ->method('only')
            ->with(['username', 'password'])
            ->willReturn($credentials);
        $request->expects($this->once())
            ->method('get')
            ->with('remember_me', 0)
            ->willReturn(1);

        $this->guard->expects($this->once())
            ->method('attempt')
            ->with($credentials + ['active' => true], 1, true)
            ->willReturn(true);

        URL::shouldReceive('to')->with('/home')->andReturn(null);
        Redirect::shouldReceive('to')->with(null);

        app()->instance('scaffold.config', $config);

        $this->controller->postLogin($request);
    }

    /** @test */
    public function it_redirects_back_when_login_failed()
    {
        $config = $this->createMock(Repository::class);
        $config->expects($this->exactly(3))
            ->method('get')
            ->willReturnCallback(fn($key, $default) => match ([$key, $default]) {
                ['auth.identity', 'username'] => 'username',
                ['auth.credential', 'password'] => 'password',
                ['auth.conditions', []] => ['active' => true],
            });

        /** @var LoginRequest|MockObject $request */
        $request = $this->createMock(LoginRequest::class);
        $credentials = [
            'username' => 'admin@example.com',
            'password' => 'secret',
        ];

        $request->expects($this->once())
            ->method('only')
            ->with(['username', 'password'])
            ->willReturn($credentials);
        $request->expects($this->once())
            ->method('get')
            ->with('remember_me', 0)
            ->willReturn(1);

        $this->guard->expects($this->once())
            ->method('attempt')
            ->with($credentials + ['active' => true], 1, true)
            ->willReturn(false);

        $translator = $this->mockTranslator();
        $translator->shouldReceive('get')->andReturn('error');

        $redirectResponse = $this->createMock(RedirectResponse::class);
        $redirectResponse->expects($this->once())->method('withErrors')->with(['error']);

        Redirect::shouldReceive('back')->andReturn($redirectResponse);

        app()->instance('scaffold.config', $config);

        $this->controller->postLogin($request);
    }
}
