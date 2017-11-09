## Forms

![Scopes](http://docs.adminarchitect.com/images/form/edit.jpg)

Admin Architect gives you a complete control over the output of the form by creating a thin DSL on top of Illuminate\Form package:

Defining of all fields for editing can by annoying, so Admin Architect eliminates that step: all `$fillable` and `$translatedAttributes` (mulilingual support) columns are available for editing.

You can extend default form by adding columns or changing the column settings:

For models, each field should be one of your model's SQL columns or one of its Eloquent relationships or any custom field (assuming you define the mechanism to handle and save it).

```
public function form()
{
    return $this
	->scaffoldForm()

	# Update existing column
	->update('user_id', function ($element) {
		# Set a different input type
		$element->setInput(
			new Select('user_id')
		);

		# set dropdown options
		$element->getInput()->setOptions(
			User::pluck('name', 'id')->toArray()
		);

		return $element;
	})

	# add a new Relation meta.keywords
	->create('meta.keywords', 'text')

	# add another Relation meta.description
	->create(
		FormElement::textarea('meta.description'),
		function ($description) {
			# Like titles, hints translations are auto-discovered by
			# asking Translator for keys:
			# 1. administrator::hints.<module>.<column>
			# 2. administrator::hints.global.<column>
			$element->setDescription('Element description');

			return $description->setTitle('Meta description');
		}
	);
}
```
`.dot` notation points to a relationship,
in case of `meta.` - it points to a HasOne relationship `Post::meta()`,

### Supported form controls
The complete list (updates constantly) of supported controls:

 * @method static FormElement text(string $name)
 * @method static FormElement view(string $name)
 * @method static FormElement search(string $name)
 * @method static FormElement textarea(string $name)
 * @method static FormElement medium(string $name)
 * @method static FormElement tinymce(string $name)
 * @method static FormElement ckeditor(string $name)
 * @method static FormElement boolean(string $name)
 * @method static FormElement radio(string $name, array $attributes, array $options)
 * @method static FormElement multiCheckbox(string $name, array $attributes = [], array $options = [])
 * @method static FormElement datalist(string $name, array $attributes = [], array $options = [])
 * @method static FormElement date(string $name)
 * @method static FormElement daterange(string $name)
 * @method static FormElement datetime(string $name)
 * @method static FormElement time(string $name)
 * @method static FormElement email(string $name)
 * @method static FormElement file(string $name)
 * @method static FormElement hidden(string $name)
 * @method static FormElement image(string $name)
 * @method static FormElement key(string $name)
 * @method static FormElement markdown(string $name)
 * @method static FormElement number(string $name)
 * @method static FormElement password(string $name)
 * @method static FormElement select(string $name, array $attributes, array $options)
 * @method static FormElement tel(string $name)

### Files & Images
Files & Images are handled by `codesleeve/laravel-stapler` library.

This is the shortest way to attach an image object to a column:

```php
class User extends Eloquent implements StaplerableInterface
{
    use EloquentTrait;

    # Add the 'avatar' attachment to the fillable array,
	# so that it's mass-assignable on this model.
    protected $fillable = ['avatar', 'cv'];

    public function __construct(array $attributes = array())
    {
        $this->hasAttachedFile('avatar', [
			# Image resizing configuration
            'styles' => [
                'medium' => '300x300',
                'thumb' => '100x100'
            ]
        ]);

		# Just upload a file
        $this->hasAttachedFile('cv');

        parent::__construct($attributes);
    }
}
```
For more info please checkout its documentation by accessing https://github.com/CodeSleeve/laravel-stapler.