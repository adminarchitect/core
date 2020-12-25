<?php

namespace Terranet\Administrator\Tests\Auth;

use App\Models\User;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Factory;
use PHPUnit\Framework\MockObject\MockObject;
use Terranet\Administrator\Auth\SuperAdminRule;
use Terranet\Administrator\Tests\CoreTestCase;

class SuperAdminRuleTest extends CoreTestCase
{
    /** @test */
    public function it_fetches_user()
    {
        $rule = $this->createMock(SuperAdminRule::class);

        $guard = $this->createMock(SessionGuard::class);
        $guard->expects($this->once())->method('user');

        $auth = $this->createPartialMock(Factory::class, ['user', 'guard', 'shouldUse']);
        $auth->expects($this->once())->method('guard')->with('admin')->willReturn($guard);

        app()->instance('Illuminate\Contracts\Auth\Factory', $auth);

        $this->invokeMethod($rule, 'userProvider');
    }

    /** @test */
    public function it_returns_false_if_no_auth_user()
    {
        /** @var MockObject|SuperAdminRule $rule */
        $rule = $this->createPartialMock(SuperAdminRule::class, ['userProvider']);
        $rule->method('userProvider')->willReturn(null);

        $this->assertFalse($rule->validate());
    }

    /** @test */
    public function it_calls_a_model_super_admin_method()
    {
        /** @var Authenticatable|MockObject $user */
        $user = $this->createPartialMock(User::class, ['isSuperAdmin']);
        $user->expects($this->once())
             ->method('isSuperAdmin')
             ->willReturn(true);

        $rule = new SuperAdminRule();
        $rule->validate($user);
    }

    /** @test */
    public function it_accepts_user_by_id()
    {
        /** @var Authenticatable|MockObject $user */
        $user = $this->createPartialMock(User::class, ['getAuthIdentifier']);
        $user->expects($this->once())
             ->method('getAuthIdentifier')
             ->willReturn(1);

        $rule = new SuperAdminRule();
        $this->assertTrue($rule->validate($user));
    }
}
