<?php

namespace Tests\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Terranet\Administrator\ActionsManager;
use Terranet\Administrator\Controllers\AdminController;
use Terranet\Administrator\Middleware\Authenticate;
use Terranet\Administrator\Middleware\AuthProvider;
use Terranet\Administrator\Middleware\Resources;
use Terranet\Administrator\Tests\CoreTestCase;
use Terranet\Administrator\Tests\MocksObjects;

class AdminControllerTest extends CoreTestCase
{
    use MocksObjects;

    /** @test */
    public function it_sets_required_middleware()
    {
        $controller = $this->getMockBuilder(AdminController::class)
                           ->onlyMethods(['middleware'])
                           ->disableOriginalConstructor()
                           ->getMock();

        $controller->expects($this->once())
                   ->method('middleware')
                   ->with([
                       AuthProvider::class,
                       Authenticate::class,
                       Resources::class,
                   ]);

        $controller->__construct($this->mockTranslator());
    }

    /** @test */
    public function it_authorizes_the_action_call()
    {
        /** @var AdminController|MockObject $controller */
        $controller = $this->getMockBuilder(AdminController::class)
                           ->onlyMethods([])
                           ->setConstructorArgs([$this->mockTranslator()])
                           ->getMock();

        $actions = $this->createPartialMock(ActionsManager::class, ['authorize']);

        $actions->expects($this->once())
                ->method('authorize')
                ->with($ability = 'users.create', null)
                ->willReturn(true);

        $module = $this->mockModule();
        $module->shouldReceive('actions')->andReturn($actions);

        $controller->authorize($ability, null);
    }

    /** @test */
    public function it_throw_unauthorized_exception()
    {
        $translator = $this->mockTranslator();
        $translator->shouldReceive('get')
                   ->with('administrator::errors.unauthorized', [], null)
                   ->andReturn('Unauthorized');

        /** @var AdminController|MockObject $controller */
        $controller = $this->getMockBuilder(AdminController::class)
                           ->onlyMethods([])
                           ->setConstructorArgs([$translator])
                           ->getMock();

        $ability = 'users.create';

        $actions = $this->createPartialMock(ActionsManager::class, ['authorize']);
        $actions->expects($this->once())
                ->method('authorize')
                ->with($ability, null)
                ->willReturn(false);

        $module = $this->mockModule();
        $module->shouldReceive('actions')->andReturn($actions);

        $this->expectException(HttpException::class);
        $controller->authorize($ability, null);
    }

    /** @test */
    public function it_redirects_back_to_provided_url()
    {
        /** @var AdminController|MockObject $controller */
        $controller = $this->getMockBuilder(AdminController::class)
                           ->onlyMethods([])
                           ->setConstructorArgs([$this->mockTranslator()])
                           ->getMock();

        $request = $this->getMockBuilder(Request::class)
                        ->disableOriginalConstructor()
                        ->onlyMethods(['get'])
                        ->disableAutoload()
                        ->getMock();

        $request->expects($this->once())
                ->method('get')
                ->with('back_to')
                ->willReturn('/users');

        $redirector = $this->createMock(Redirector::class);
        $redirector->expects($this->once())
                   ->method('to')
                   ->with('/users');

        app()->instance('redirect', $redirector);

        $this->invokeMethod($controller, 'redirectTo', ['users', 1, $request]);
    }

    /** @test */
    public function it_redirects_back_to_editable_model()
    {
        /** @var AdminController|MockObject $controller */
        $controller = $this->getMockBuilder(AdminController::class)
                           ->onlyMethods([])
                           ->setConstructorArgs([$this->mockTranslator()])
                           ->getMock();

        $request = $this->getMockBuilder(Request::class)
                        ->disableOriginalConstructor()
                        ->onlyMethods(['get', 'exists'])
                        ->disableAutoload()
                        ->getMock();

        $request->expects($this->once())
                ->method('get')
                ->with('back_to')
                ->willReturn(null);

        $request->expects($this->once())
                ->method('exists')
                ->with('save')
                ->willReturn(true);

        $redirector = $this->createMock(Redirector::class);
        $redirector->expects($this->once())
                   ->method('route')
                   ->with('scaffold.edit', ['module' => 'users', 'id' => 1]);

        app()->instance('redirect', $redirector);

        $this->invokeMethod($controller, 'redirectTo', ['users', 1, $request]);
    }

    /** @test */
    public function it_redirects_back_to_index_table()
    {
        /** @var AdminController|MockObject $controller */
        $controller = $this->getMockBuilder(AdminController::class)
                           ->setConstructorArgs([$this->mockTranslator()])
                           ->getMock();

        $request = $this->getMockBuilder(Request::class)
                        ->disableOriginalConstructor()
                        ->onlyMethods(['get', 'exists'])
                        ->disableAutoload()
                        ->getMock();

        $request->expects($this->exactly(1))
                ->method('get')
                ->with('back_to')
                ->willReturn(null);

        $request->expects($this->exactly(2))
            ->method('exists')
            ->willReturnCallback(fn($param) => match ($param) {
                'save' => false,
                'save_return' => true,
            });

        $redirector = $this->createMock(Redirector::class);
        $redirector->expects($this->once())
                   ->method('route')
                   ->with('scaffold.index');

        app()->instance('redirect', $redirector);

        $this->invokeMethod($controller, 'redirectTo', ['users', 1, $request]);
    }
}
