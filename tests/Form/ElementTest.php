<?php

use Terranet\Administrator\Form\FormElement;
use Terranet\Administrator\Form\Type\Text;

require_once __DIR__.'/../MocksValidator.php';
require_once __DIR__.'/../MocksObjects.php';

class ElementTest extends PHPUnit\Framework\TestCase
{
    use MocksValidator, MocksObjects;

    public function setUp()
    {
        parent::setUp();

        $this->mockValidator();
        $this->mockTranslator();
        $this->mockModule();
    }

    /** @test */
    public function it_creates_a_form_element_by_calling_static_method()
    {
        $element = FormElement::text('name');

        $this->assertSame($element->id(), 'name');
        $this->assertInstanceOf(Text::class, $element->getInput());
    }
}
