<?php

use Terranet\Administrator\Form\FormElement;

require_once __DIR__ . '/../MocksValidator.php';

class FormElementTest extends PHPUnit_Framework_TestCase
{
    use MocksValidator;

    public function setUp()
    {
        parent::setUp();

        $this->mockValidator();
    }

    /** @test */
    public function it_creates_appropriate_form_element()
    {
        $elements = [
            'text' => \Terranet\Administrator\Form\Type\Text::class,
            'textarea' => \Terranet\Administrator\Form\Type\Textarea::class,
            'search' => \Terranet\Administrator\Form\Type\Search::class,
            'tinymce' => \Terranet\Administrator\Form\Type\Tinymce::class,
            'ckeditor' => \Terranet\Administrator\Form\Type\Ckeditor::class,
            'boolean' => \Terranet\Administrator\Form\Type\Boolean::class,
            'number' => \Terranet\Administrator\Form\Type\Number::class,
            'datalist' => \Terranet\Administrator\Form\Type\Datalist::class,
            'date' => \Terranet\Administrator\Form\Type\Date::class,
            'daterange' => \Terranet\Administrator\Form\Type\Daterange::class,
            'datetime' => \Terranet\Administrator\Form\Type\Datetime::class,
            'time' => \Terranet\Administrator\Form\Type\Time::class,
            'email' => \Terranet\Administrator\Form\Type\Email::class,
            'file' => \Terranet\Administrator\Form\Type\File::class,
            'image' => \Terranet\Administrator\Form\Type\Image::class,
            'hidden' => \Terranet\Administrator\Form\Type\Hidden::class,
            'key' => \Terranet\Administrator\Form\Type\Key::class,
        ];

        foreach ($elements as $type => $instance) {
            $element = FormElement::$type('test');

            $input = $element->getInput();

            $this->assertInstanceOf(FormElement::class, $element);
            $this->assertInstanceOf($instance, $input);
        }
    }

    /** @test */
    public function it_allows_options_for_select_and_datalist_elements()
    {
        $select = FormElement::select('select');
        $datalist = FormElement::datalist('datalist');
        $select->getInput()->setOptions($options = [1, 2, 3, 4, 5]);
        $datalist->getInput()->setOptions($options);

        $this->assertEquals(
            $options, $select->getInput()->getOptions()
        );

        $this->assertEquals(
            $options, $datalist->getInput()->getOptions()
        );
    }

    /** @test */
    public function it_allows_callable_options_for_select_and_datalist_elements()
    {
        $select = FormElement::select('select');
        $datalist = FormElement::datalist('datalist');

        $realOptions = [1, 2, 3, 4, 5];
        $options = function () use ($realOptions) {
            return $realOptions;
        };

        $select->getInput()->setOptions($options);
        $datalist->getInput()->setOptions($options);

        $this->assertEquals(
            $realOptions, $select->getInput()->getOptions()
        );

        $this->assertEquals(
            $realOptions, $datalist->getInput()->getOptions()
        );
    }

    /** @test */
    public function it_allows_a_data_url_for_search_form_element()
    {
        $search = FormElement::search('user');
        $search->getInput()->setDataUrl('/search/users');

        $this->assertEquals(
            '/search/users', $search->getInput()->getDataUrl()
        );
        $this->assertArrayHasKey('data-type', $attributes = $search->getInput()->getAttributes());
        $this->assertEquals('livesearch', $attributes['data-type']);
    }

}