<?php

namespace Terranet\Administrator\Tests\Actions;

use App\User;
use Terranet\Administrator\Actions\Collection;
use Terranet\Administrator\Actions\RemoveSelected;
use Terranet\Administrator\Actions\SaveOrder;
use Terranet\Administrator\Tests\CoreTestCase;

class CollectionTest extends CoreTestCase
{
    /** @test */
    public function it_accepts_items_on_creation()
    {
        $collection = new Collection([RemoveSelected::class, SaveOrder::class]);

        $this->assertCount(2, $collection);
        $this->assertInstanceOf(RemoveSelected::class, $collection->get(0));
        $this->assertInstanceOf(SaveOrder::class, $collection->get(1));
    }

    /** @test */
    public function it_finds_an_item_by_name()
    {
        $collection = new Collection([RemoveSelected::class, SaveOrder::class]);

        $this->assertInstanceOf(SaveOrder::class, $collection->find('save_order'));
        $this->assertInstanceOf(RemoveSelected::class, $collection->find('remove_selected'));
    }

    /** @test */
    public function it_calls_the_action_authorize_method()
    {
        $user = new User();
        $model = new User();

        $allow = new class() {
            public function authorize(User $user)
            {
                return true;
            }
        };

        $deny = new class() {
            public function authorize(User $user)
            {
                return false;
            }
        };

        $collection = new Collection([$allow, $deny]);
        $this->assertCount(
            1,
            $collection->authorized($user, $model)
        );
    }

    /** @test */
    public function it_allows_authorization_free_actions()
    {
        $user = new User();
        $model = new User();

        $first = new class() {
            // do not provides `authorize` method
        };
        $second = new class() {
            // do not provides `authorize` method
        };

        $collection = new Collection([$first, $second]);
        $this->assertCount(
            2,
            $collection->authorized($user, $model)
        );
    }
}
