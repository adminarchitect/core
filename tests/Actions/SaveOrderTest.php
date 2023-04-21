<?php

namespace Tests\Actions;

use App\Models\User;
use Illuminate\Http\Request;
use Terranet\Administrator\Actions\SaveOrder;
use Terranet\Administrator\Tests\CoreTestCase;

class SaveOrderTest extends CoreTestCase
{
    /** @test */
    public function it_calls_eloquent_method()
    {
        $action = new SaveOrder();

        $request = $this->createMock(Request::class);
        $request->expects($this->once())->method('get')->with('rank')->willReturn([]);

        $eloquent = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->addMethods(['syncRanking', 'getRankableColumn'])
            ->getMock();

        $eloquent->expects($this->once())->method('syncRanking')->with([]);
        $eloquent->expects($this->once())->method('getRankableColumn')->willReturn('rank');

        $action->handle($eloquent, $request);
    }

    /** @test */
    public function it_may_have_an_icon()
    {
        $action = $this->createMock(SaveOrder::class);

        $this->assertNotEmpty(
            $this->invokeMethod($action, 'icon')
        );
    }
}
