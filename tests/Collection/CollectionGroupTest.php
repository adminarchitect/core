<?php

require_once __DIR__.'/../CreatesElement.php';
require_once __DIR__.'/../MocksObjects.php';

use Terranet\Administrator\Collection\Group;

/**
 * @coversNothing
 */
class CollectionGroupTest extends PHPUnit\Framework\TestCase
{
    use CreatesElement, MocksObjects;

    /**
     * @var Group
     */
    protected $group;

    public function setUp()
    {
        parent::setUp();

        $this->createGroup('test');
    }

    /** @test */
    public function it_returns_id_and_title()
    {
        $this->group->setTranslator($this->mockTranslator());

        $this->assertSame(
            'test',
            $this->group->id()
        );

        $this->assertSame(
            'Test',
            $this->group->title()
        );
    }

    /** @test */
    public function it_excludes_stop_words_from_the_title()
    {
        $group = $this->createGroup('group_id');
        $group->setTranslator($this->mockTranslator());
        $group->setModule($this->mockModule());

        $this->assertSame(
            'Group',
            $group->title()
        );
    }

    /** @test */
    public function it_allows_to_change_a_title()
    {
        $this->group->setTitle($title = 'New title');

        $this->assertSame(
            $title,
            $this->group->title()
        );
    }

    /** @test */
    public function it_allows_elements()
    {
        $this->group->push($this->e('first'))->push($this->e('second'));

        $this->assertCount(2, $this->group->elements());
    }

    protected function createGroup($title)
    {
        $this->group = new Group($title);

        $this->group->setTranslator($this->mockTranslator());
        $this->group->setModule($this->mockModule());

        return $this->group;
    }
}
