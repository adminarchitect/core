<?php

use Terranet\Administrator\Form\Collection\Mutable;
use Terranet\Administrator\Form\FormElement;
use Terranet\Administrator\Form\Type\Tinymce;

require_once __DIR__.'/../../MocksValidator.php';
require_once __DIR__.'/../../MocksObjects.php';

/**
 * @coversNothing
 */
class FormCollectionMutableTest extends PHPUnit\Framework\TestCase
{
    use MocksValidator, MocksObjects;

    public function setUp()
    {
        parent::setUp();

        $this->mockValidator();
    }

    /** @test */
    public function it_creates_a_form_element_from_string_id()
    {
        $collection = new Mutable();

        $collection
            ->create('title')
            ->create('body', 'text', 'before:title');

        $this->assertCount(
            2,
            $collection->all()
        );

        $this->assertSame(
            $collection->find('title'),
            new FormElement('title')
        );

        return $collection;
    }

    /**
     * @test
     * @depends it_creates_a_form_element_from_string_id
     *
     * @param Mutable $collection
     */
    public function it_sets_text_input_as_default_type($collection)
    {
        $this->assertSame(
            $collection->find('title')->getInput(),
            new \Terranet\Administrator\Form\Type\Text('title')
        );
    }

    /**
     * @test
     * @depends  it_creates_a_form_element_from_string_id
     *
     * @param Mutable $collection
     */
    public function it_sets_proper_position_to_a_new_created_element($collection)
    {
        $this->assertSame(
            0,
            $collection->position('body')
        );
    }

    /** @test */
    public function it_creates_a_form_element_from_object()
    {
        $collection = new Mutable();

        $collection
            ->create(
                $title = new FormElement('title')
            )
            ->create(
                $body = new FormElement('body')
            );

        $this->assertCount(
            2,
            $collection->all()
        );

        $this->assertSame(
            [$title, $body],
            $collection->all()
        );

        return $collection;
    }

    /**
     * @test
     * @depends  it_creates_a_form_element_from_object
     *
     * @param Mutable $collection
     *
     * @return Mutable
     */
    public function it_allows_editing_of_new_created_element($collection)
    {
        $collection->create(
            $desc = new FormElement('description'),
            function ($element) {
                $element->setTranslator($this->mockTranslator());
                $element->setModule($this->mockModule());
                $element->setTitle('New description');
                $element->setInput(
                    (new Tinymce($element->id()))
                )->setDescription('Describe your personality');
            }
        );

        $this->assertSame(
            'New description',
            $collection->find('description')->title()
        );

        $this->assertSame(
            'Describe your personality',
            $collection->find('description')->getDescription()
        );

        return $collection;
    }

    /**
     * @test
     * @depends it_allows_editing_of_new_created_element
     *
     * @param Mutable $collection
     */
    public function it_checks_for_editors_presence($collection)
    {
        $this->assertTrue(
            $collection->hasEditors('tinymce')
        );
    }
}
