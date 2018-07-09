<?php

require_once __DIR__.'/../CreatesElement.php';
require_once __DIR__.'/../MocksObjects.php';

use Terranet\Administrator\Collection\Group;
use Terranet\Administrator\Collection\Mutable;
use Terranet\Administrator\Columns\Element;

class CollectionMutableTest extends PHPUnit\Framework\TestCase
{
    use CreatesElement, MocksObjects;

    /**
     * @var Mutable
     */
    protected $collection;

    public function setUp()
    {
        parent::setUp();

        $this->mockTranslator();
        $this->mockModule();
        $this->collection = $this->makeElementsCollection();
    }

    /** @test */
    public function it_initializes_a_collection()
    {
        $this->assertCount(3, $this->collection);

        $this->assertSame(
            $this->collection->toArray(),
            [
                $this->e('first'),
                $this->e('second'),
                $this->e('third'),
            ]
        );
    }

    /** @test */
    public function it_inserts_an_item_to_a_collection()
    {
        $this->collection->insert($this->e('fifth'), 0);

        $this->assertSame(
            $this->collection->toArray(),
            [
                $this->e('fifth'),
                $this->e('first'),
                $this->e('second'),
                $this->e('third'),
            ]
        );

        $this->collection->insert($this->e('sixth'), 2);
        $this->assertSame(
            $this->collection->toArray(),
            [
                $this->e('fifth'),
                $this->e('first'),
                $this->e('sixth'),
                $this->e('second'),
                $this->e('third'),
            ]
        );
    }

    /** @test */
    public function it_removes_an_element_from_collection()
    {
        $this->assertCount(2, $this->collection->without('first'));

        $this->assertSame(
            $this->collection->toArray(),
            [
                $this->e('second'),
                $this->e('third'),
            ]
        );
    }

    /** @test */
    public function it_removes_many_elements_at_once_from_collection()
    {
        $this->collection->without(['first', 'second']);

        $this->assertCount(1, $this->collection);

        $this->assertSame(
            $this->collection->toArray(),
            [
                $this->e('third'),
            ]
        );
    }

    /** @test */
    public function it_can_update_a_collection_value()
    {
        $this->collection->update('first', function (Element $element) {
            $element->setTranslator($this->mockTranslator());
            $element->setModule($this->mockModule());

            return $element->setTitle('First Element');
        });

        $this->assertSame(
            'First Element',
            $this->collection->find('first')->title()
        );
    }

    /** @test */
    public function it_can_update_many_items_in_a_collection()
    {
        $this->collection->updateMany([
            'first' => function ($element) {
                $element->setTranslator($this->mockTranslator());
                $element->setModule($this->mockModule());
                $element->setTitle('First Element');
            },
            'third' => function ($element) {
                $element->setTranslator($this->mockTranslator());
                $element->setModule($this->mockModule());
                $element->setTitle('Third Element');
            },
        ]);

        $this->assertSame(
            'First Element',
            $this->collection->toArray()[0]->title()
        );
        $this->assertSame(
            'Third Element',
            $this->collection->toArray()[2]->title()
        );
    }

    /** @test */
    public function it_moves_an_element_to_a_position()
    {
        $this->assertSame(
            $this->collection->move('first', 1)->toArray(),
            [
                $this->e('second'),
                $this->e('first'),
                $this->e('third'),
            ]
        );

        $this->assertSame(
            $this->collection->move('second', 2)->toArray(),
            [
                $this->e('first'),
                $this->e('third'),
                $this->e('second'),
            ]
        );
    }

    /** @test */
    public function it_moves_an_element_before_another_element()
    {
        $this->collection->move('third', 'before:first');

        $this->assertSame(
            $this->collection->toArray(),
            [
                $this->e('third'),
                $this->e('first'),
                $this->e('second'),
            ]
        );

        $this->collection->move('first', 'before:third');
        $this->assertSame(
            $this->collection->toArray(),
            [
                $this->e('first'),
                $this->e('third'),
                $this->e('second'),
            ]
        );
    }

    /** @test */
    public function it_moves_an_element_after_another_element()
    {
        $this->collection->move('first', 'after:third');
        $this->assertSame(
            $this->collection->toArray(),
            [
                $this->e('second'),
                $this->e('third'),
                $this->e('first'),
            ]
        );
    }

    /** @test */
    public function it_creates_a_group_of_elements()
    {
        $this->collection->group('first_group', function (Group $group) {
            $group->push($this->e('first'));
            $group->push($this->e('second'));
        });

        $this->assertCount(
            4,
            $this->collection
        );

        $group = $this->collection->find('first_group');

        $this->assertInstanceOf('Terranet\Administrator\Collection\Group', $group);
        $this->assertCount(2, $group->elements());
    }

    /** @test */
    public function it_joins_two_or_more_elements_into_a_group()
    {
        $this->collection
            ->join(['first', 'third'], 'group')
            ->move('group', 'after:second');

        $this->assertCount(2, $this->collection);

        $this->assertInstanceOf(Group::class, $this->collection->get(1));
        $this->assertSame($this->collection->get(1)->elements()[0]->id(), 'first');
        $this->assertSame($this->collection->get(1)->elements()[1]->id(), 'third');
    }

    /**
     * @return Mutable
     */
    protected function makeElementsCollection()
    {
        $collection = (new \Terranet\Administrator\Collection\Mutable());

        return $collection
            ->push($this->e('first'))
            ->push($this->e('second'))
            ->push($this->e('third'));
    }
}
