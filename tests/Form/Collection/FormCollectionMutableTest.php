<?php

namespace Terranet\Administrator\Tests\Form\Collection;

use Terranet\Administrator\Field\Text;
use Terranet\Administrator\Field\Textarea;
use Terranet\Administrator\Form\Collection\Mutable;
use Terranet\Administrator\Tests\MocksObjects;

class FormCollectionMutableTest extends \PHPUnit\Framework\TestCase
{
    use MocksObjects;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockTranslator();
        $this->mockModule();
    }

    /** @test */
    public function it_creates_a_form_element_from_string_id()
    {
        $collection = new Mutable();
        $collection->add('title');

        $this->assertCount(1, $collection->all());
        $this->assertInstanceOf(Text::class, $collection->find('title'));

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
        $this->assertInstanceOf(Text::class, $collection->find('title'));
    }

    /**
     * @test
     * @depends  it_creates_a_form_element_from_string_id
     *
     * @param Mutable $collection
     */
    public function it_sets_proper_position_to_a_new_created_element($collection)
    {
        $collection->insert(Textarea::make('body'), 'before:title');
        $this->assertSame(0, $collection->position('body'));
    }

    /** @test */
    public function it_creates_a_form_element_from_object()
    {
        $collection = new Mutable();

        $collection
            ->add($title = Text::make('title'))
            ->add($body = Textarea::make('body'));

        $this->assertCount(2, $collection->all());
        $this->assertSame([$title, $body], $collection->all());

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
        $collection->add(
            $desc = Textarea::make('description'),
            function (Textarea $element) {
                $element->setTitle('New description');
                $element->tinymce()
                        ->setDescription('Describe your personality');

                return $element;
            }
        );

        $e = $collection->find('description');

        $this->assertSame(
            'New description',
            $e->title()
        );

        $this->assertSame(
            'Describe your personality',
            $e->getDescription()
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
        $this->assertTrue($collection->hasEditors('tinymce'));
    }
}
