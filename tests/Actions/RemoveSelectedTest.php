<?php

namespace Tests\Actions;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use PHPUnit\Framework\MockObject\MockObject;
use Terranet\Administrator\Actions\RemoveSelected;
use Terranet\Administrator\Contracts\ActionsManager;
use Terranet\Administrator\Tests\CoreTestCase;
use Terranet\Administrator\Tests\MocksObjects;

class RemoveSelectedTest extends CoreTestCase
{
    use MocksObjects;

    /** @test */
    public function it_authorizes_delete_action()
    {
        $actions = $this->createMock(ActionsManager::class);
        $actions->expects($this->once())->method('authorize')->with('delete', $user = new User());

        $module = $this->mockModule();
        $module->shouldReceive('actions')->andReturn($actions);

        $action = $this->createMock(RemoveSelected::class);
        $this->invokeMethod($action, 'canDelete', [$user]);
    }

    /** @test */
    public function it_removes_authorized_models()
    {
        /** @var MockObject|Model $user */
        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('delete');

        /** @var MockObject|RemoveSelected $action */
        $action = $this->createPartialMock(RemoveSelected::class, ['canDelete', 'fetchForDelete']);
        $action->expects($this->once())->method('fetchForDelete')->willReturn(new Collection([$user]));
        $action->expects($this->once())->method('canDelete')->with($user)->willReturn(true);

        /** @var MockObject|Request $request */
        $request = $this->createPartialMock(Request::class, ['get']);

        /** @var MockObject|Model $model */
        $model = $this->createMock(Model::class);

        $action->handle($model, $request);
    }

    /** @test */
    public function it_fetches_models_for_deletion()
    {
        $action = $this->createPartialMock(RemoveSelected::class, []);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())->method('get')->with('collection')->willReturn([]);

        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())->method('get')->willReturn(new Collection());
        $builder->expects($this->once())->method('whereIn')->with('id', [])->willReturn($builder);

        $model = $this->createMock(User::class);
        $model->expects($this->once())->method('newQueryWithoutScopes')->willReturn($builder);

        $this->invokeMethod($action, 'fetchForDelete', [$model, $request]);
    }

    /** @test */
    public function it_may_have_an_icon()
    {
        $action = $this->createMock(RemoveSelected::class);

        $this->assertNotEmpty(
            $this->invokeMethod($action, 'icon')
        );
    }
}
