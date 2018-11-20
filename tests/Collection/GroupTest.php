<?php

namespace Terranet\Administrator\Tests\Collection;

use Terranet\Administrator\Collection\Group;
use Terranet\Administrator\Field\Generic;
use Terranet\Administrator\Tests\CreatesElement;
use Terranet\Administrator\Tests\MocksObjects;

class GroupTest extends \PHPUnit\Framework\TestCase
{
    use CreatesElement, MocksObjects;

    public function setUp()
    {
        parent::setUp();

        $this->mockTranslator();
        $this->mockModule();
    }

    /** @test */
    public function it_returns_id_and_title()
    {
        $group = new Group('test');

        $this->assertSame('test', $group->id());
        $this->assertSame('Test', $group->title());
    }

    /** @test */
    public function it_excludes_stop_words_from_the_title()
    {
        $group = new Group('group_id');
        $this->assertSame('Group', $group->title());
    }

    /** @test */
    public function it_allows_to_change_a_title()
    {
        $group = new Group('test');
        $group->setTitle($title = 'New title');

        $this->assertSame($title, $group->title());
    }

    /** @test */
    public function it_allows_elements()
    {
        $group = new Group('test');
        $group->push($this->e('first'))
              ->push($this->e('second'));

        $this->assertCount(2, $group->elements());
    }

    /** @test */
    public function it_merges_elements()
    {
        $group = new Group('test');
        $group->push($this->e('first'))
              ->push($this->e('second'));

        $group->merge([$this->e('third'), $this->e('fourth')]);

        $this->assertCount(4, $group->elements());
    }

    /** @test */
    public function it_inserts_an_item_to_a_specific_position()
    {
        $group = new Group('test');
        $group->push($this->e('first'))
              ->push($this->e('third'));

        $group->insert($e = $this->e('second'), 1);

        $this->assertSame($e, $group->elements()->get(1));
    }

    /** @test */
    public function it_excludes_an_element()
    {
        $group = new Group('test');
        $group->push($first = $this->e('first'))
              ->push($this->e('second'));

        $filtered = $group->except('second');

        $this->assertCount(1, $filtered->elements());
        $this->assertSame($first, $filtered->elements()->first());
    }

    /** @test */
    public function it_updates_an_element()
    {
        $group = new Group('test');
        $group->push($this->e('first'));

        $group->update('first', function (Generic $e) {
            $e->setTitle('second');

            return $e;
        });

        $this->assertCount(1, $group->elements());
        $this->assertSame('second', $group->elements()->first()->title());
    }

    /** @test */
    public function it_updates_many_elements_at_once()
    {
        $group = new Group('test');
        $group->push($this->e('first'));
        $group->push($this->e('second'));

        $group->updateMany([
            'first' => function ($e) {
                $e->setTitle('first modified');

                return $e;
            },

            'second' => function ($e) {
                $e->setTitle('second modified');

                return $e;
            },
        ]);

        $this->assertCount(2, $group->elements());
        $this->assertSame('first modified', $group->elements()->get(0)->title());
        $this->assertSame('second modified', $group->elements()->get(1)->title());
    }

    /** @test */
    public function it_moves_an_item_to_a_specific_position()
    {
        $group = new Group('test');
        $group->push($this->e('first'))
              ->push($this->e('second'))
              ->push($this->e('third'));

        $group->move('second', 'after:third');

        $this->assertSame($this->e('second'), $group->elements()->get(2));
    }

    /** @test */
    public function it_finds_an_element_by_id()
    {
        $group = new Group('test');
        $group->push($this->e('first'))
              ->push($this->e('second'))
              ->push($this->e('third'));

        $this->assertSame($this->e('second'), $group->find('second'));
    }

    /** @test */
    public function it_maps_elements()
    {
        $group = new Group('test');
        $group->push($this->e('first'))
              ->push($this->e('second'))
              ->push($this->e('third'));

        $group = $group->map(function ($e) {
            $e->setTitle("{$e->id()} modified");

            return $e;
        });

        $this->assertSame('first modified', $group->elements()->get(0)->title());
        $this->assertSame('second modified', $group->elements()->get(1)->title());
        $this->assertSame('third modified', $group->elements()->get(2)->title());
    }
}
