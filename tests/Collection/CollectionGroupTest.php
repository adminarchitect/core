<?php

require_once __DIR__.'/../CreatesElement.php';
require_once __DIR__.'/../MocksObjects.php';

use Terranet\Administrator\Collection\Group;

class CollectionGroupTest extends \PHPUnit\Framework\TestCase
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
}
