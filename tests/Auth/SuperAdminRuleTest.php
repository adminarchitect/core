<?php

class SuperAdminRuleTest extends \Illuminate\Foundation\Testing\TestCase
{
    use \Tests\CreatesApplication;

    /** @test */
    public function it_returns_false_if_no_auth_user()
    {
        \Illuminate\Support\Facades\Auth::shouldReceive('user')->andReturn(null);

        $rule = new \Terranet\Administrator\Auth\SuperAdminRule();
        $this->assertFalse($rule->validate());
    }

    /** @test */
    public function it_calls_a_model_super_admin_method()
    {
        $user = $this->createPartialMock(\App\User::class, ['isSuperAdmin']);

        $user->expects($this->once())
             ->method('isSuperAdmin')
             ->willReturn(true);

        $rule = new \Terranet\Administrator\Auth\SuperAdminRule();
        $rule->validate($user);
    }

    /** @test */
    public function it_accepts_user_by_id()
    {
        $user = $this->createPartialMock(\App\User::class, ['getAuthIdentifier']);

        $user->expects($this->once())
             ->method('getAuthIdentifier')
             ->willReturn(1);

        $rule = new \Terranet\Administrator\Auth\SuperAdminRule();
        $this->assertTrue($rule->validate($user));
    }
}