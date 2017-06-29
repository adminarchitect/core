<?php

use Terranet\Administrator\Form\FormElement;
use Terranet\Administrator\Form\Type\Text;

require_once __DIR__ . '/../MocksValidator.php';

class ElementTest extends PHPUnit_Framework_TestCase
{
    use MocksValidator;

    public function setUp()
    {
        parent::setUp();

        $this->mockValidator();
    }

    /** @test */
    public function it_creates_a_form_element_by_calling_static_method()
    {
        $element = FormElement::text('name');

        $this->assertEquals(
            $element->id(),
            'name'
        );

        $this->assertEquals(
            $element->getInput(),
            new Text('name')
        );
    }
}