<?php

namespace Tests\Columns;

use Illuminate\Translation\Translator;
use PHPUnit\Framework\MockObject\MockObject;
use Terranet\Administrator\Field\Text;
use Terranet\Administrator\Modules\Users;
use Terranet\Administrator\Scaffolding;
use Terranet\Administrator\Tests\CoreTestCase;
use Terranet\Administrator\Tests\MocksObjects;

class ElementTest extends CoreTestCase
{
    use MocksObjects;

    /** @var MockObject|Translator */
    private $translator;

    /** @var MockObject|Scaffolding */
    private $module;

    public function setUp()
    {
        parent::setUp();

        $this->translator = $this->mockTranslator();
        $this->module = $this->mockModule();
    }

    /** @test */
    public function it_makes_an_element_as_sortable()
    {
        $callback = function () {
        };

        $module = $this->createPartialMock(Users::class, ['addSortable']);
        $module->expects($this->once())
               ->method('addSortable')
               ->with('name', $callback);

        app()->instance('scaffold.module', $module);

        Text::make('name')->sortable($callback);
    }

    /** @test */
    public function it_removes_an_element_from_sortables()
    {
        $module = $this->createPartialMock(Users::class, ['removeSortable']);
        $module->expects($this->once())
               ->method('removeSortable')
               ->with('name');

        app()->instance('scaffold.module', $module);

        Text::make('name')->disableSorting();
    }
}
