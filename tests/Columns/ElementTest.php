<?php

namespace Tests\Columns;

use App\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Translation\Translator;
use Illuminate\View\View;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use Terranet\Administrator\Columns\Decorators\CellDecorator;
use Terranet\Administrator\Columns\Decorators\StringDecorator;
use Terranet\Administrator\Columns\Element;
use Terranet\Administrator\Modules\Users;
use Terranet\Administrator\Scaffolding;
use Terranet\Administrator\Tests\CoreTestCase;
use Terranet\Administrator\Tests\MocksObjects;

class ElementTest extends CoreTestCase
{
    use MocksObjects;

    /** @var Translator|MockObject */
    private $translator;

    /** @var Scaffolding|MockObject */
    private $module;

    public function setUp()
    {
        parent::setUp();

        $this->translator = $this->mockTranslator();
        $this->module = $this->mockModule();
    }

    /** @test */
    public function it_accepts_a_template_name()
    {
        $element = new Element('test');
        $element->display('test.template');

        $reflection = new ReflectionClass($element);
        $templateProperty = $reflection->getProperty('template');
        $templateProperty->setAccessible(true);

        $this->assertSame('test.template', $templateProperty->getValue($element));
    }

    /** @test */
    public function it_is_not_a_group()
    {
        $this->assertFalse(
            (new Element('test'))->isGroup()
        );
    }

    /** @test */
    public function it_returns_a_value_if_no_template_defined()
    {
        $eloquent = new User(['name' => 'John Doe']);

        $element = new Element('name');

        $this->assertEquals(
            'John Doe', $element->render($eloquent)
        );
    }

    /** @test */
    public function it_accepts_a_view_as_a_template()
    {
        $eloquent = new User(['name' => 'John Doe']);

        $element = new Element('name');

        /** @var Renderable|MockObject $template */
        $template = $this->createMock(View::class);
        $template->expects($this->once())
                 ->method('with')
                 ->with([
                     'renderable' => $element,
                     'eloquent' => $eloquent,
                 ]);

        $element->display($template);

        $element->render($eloquent);
    }

    /** @test */
    public function it_accepts_a_decorator_as_a_template()
    {
        $eloquent = new User(['name' => 'John Doe']);

        $element = new Element('name');

        $callback = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $callback->expects($this->once())->method('__invoke');

        /** @var CellDecorator|MockObject $template */
        $template = $this->createMock(StringDecorator::class);
        $template->expects($this->once())
                 ->method('getDecorator')
                 ->willReturn($callback);

        $element->display($template);

        $element->render($eloquent);
    }

    /** @test */
    public function it_accepts_a_closure_as_a_template()
    {
        $eloquent = new User(['name' => 'John Doe']);

        $element = new Element('name');

        $closure = function ($eloquent) {
            return 'Closure output';
        };

        $element->display($closure);

        $element->render($eloquent);

        $this->assertSame(
            'Closure output', $element->render($eloquent)
        );
    }

    /** @test */
    public function it_accepts_a_string_with_placeholders_as_a_template()
    {
        $eloquent = new User(['name' => 'John Doe']);

        $element = new Element('name');
        $element->display('<a href="#">(:name)</a>');

        $element->render($eloquent);

        $this->assertSame(
            '<a href="#">John Doe</a>', $element->render($eloquent)
        );
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

        (new Element('name'))->sortable($callback);
    }

    /** @test */
    public function it_removes_an_element_from_sortables()
    {
        $module = $this->createPartialMock(Users::class, ['removeSortable']);
        $module->expects($this->once())
               ->method('removeSortable')
               ->with('name');

        app()->instance('scaffold.module', $module);

        (new Element('name'))->unSortable();
    }

    /** @test */
    public function it_fetches_a_value_from_eloquent_object()
    {
        $eloquent = new User(['name' => 'John Doe']);

        $element = new Element('name');

        $this->assertSame(
            'John Doe', $this->invokeMethod($element, 'fetchValue', [$eloquent])
        );
    }

    /** @test */
    public function it_fetches_a_value_from_eloquent_countable_relation()
    {
        $eloquent = new User(['name' => 'John Doe']);

        $element = $this->getMockBuilder(Element::class)
                        ->setMethods(['hasRelation', 'isCountableRelation', 'fetchRelationValue'])
                        ->setConstructorArgs(['name'])
                        ->getMock();

        $element->method('hasRelation')->willReturn('profile');
        $element->method('isCountableRelation')->willReturn(true);
        $element->expects($this->once())
                ->method('fetchRelationValue')
                ->with($eloquent, 'name', ['name' => 'profile'], true);

        $this->invokeMethod($element, 'fetchValue', [$eloquent]);
    }

    /** @test */
    public function it_fetches_a_value_from_eloquent_relation()
    {
        $eloquent = new User(['name' => 'John Doe']);

        $element = $this->getMockBuilder(Element::class)
                        ->setMethods(['hasRelation', 'isCountableRelation', 'fetchRelationValue'])
                        ->setConstructorArgs(['profile.name'])
                        ->getMock();

        $element->expects($this->once())
                ->method('fetchRelationValue')
                ->with($eloquent, 'name', ['profile'], true);

        $this->invokeMethod($element, 'fetchValue', [$eloquent]);
    }
}