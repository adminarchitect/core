## Forms

![Scopes](http://docs.adminarchitect.com/docs/images/form/edit.jpg)

Admin Architect gives you a complete control over the output of the form by creating a thin DSL on top of Illuminate\Form package:

Defining of all fields for editing can by annoying, so Admin Architect eliminates that step: all `$fillable` columns are available for editing.

You can extend default form by adding columns or changing the column settings:

For models, each field should be one of your model's SQL columns or one of its Eloquent relationships or any custom field (assuming you know how to handle and save it).

The order in which they are given is the order in which the admin user will see them.

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

### Input types

Along with reserved keys like: `type`, `label`, `description`, `options (for type select)`  you're free to use any of html specific tags.

So for numbers you can use, `min`, `max`, `step`, etc...
For text type you'll be able to use `placeholder`, `title`, `minlenght`, `maxlength`, etc...

#### Key

The key field type can be used to show the primary key's or unEditable value.
You cannot make this field editable since primary key values are handled internally by your database.

```
FormElement::key('name')
```

#### Text

The text field type should be any text-like type in your database. text is the default field type.

```
FormElement::text('name')
```

#### Search

A livesearch input type, allowing live searching.

```
FormElement::search('name')->getInput()->setDataUrl('/search/users')
```

#### Password

The password field type should be any text-like type in your database.

You should use Eloquent mutators in conjunction with a password field to make sure that the supplied password is properly hashed.

Or just enable the `manage_passwords` feature in `config/administrator.php` to enable handling this by Admin Architect.

```
FormElement::password('name')
```

#### Textarea

The textarea field type should be any text-like type in your database. In the edit form, an admin user will be presented with a textarea.
The limit option lets you set a character limit for the field. The height option lets you set the height of the textarea in pixels.

```
FormElement::textarea('name')
```

#### Tinymce, CKEditor

The tinymce, ckeditor field types should be a TEXT type in your database.

In the edit form, an admin user will be presented with a Tinymce OR CKEditor WYSIWYG.

When the field is saved to the database, the resulting HTML is stored in the TEXT field.

```
FormElement::tinymce('name')
FormElement::ckedidtor('name')
```

#### Number

The number field type should be a numeric type in your database.

In the edit form, an admin user will be presented with a text input. This text input will force your users to enter a number in the proper format.

The min, max and step options lets you set the &lt;input type="number" /&gt; attributes

```
FormElement::number('name')
```

#### Boolean

The boolean field type should be represented as an integer field in your database. 

Usually schema creators allow you to choose BOOLEAN which resolves to something like TINYINT(1).

This field will work as long as you can put integer 1s and 0s in your database field.

In the edit form, an admin user will be presented with a checkbox

```
FormElement::boolean('name')
```

#### Select, Datalist

The select field type should be any text-like type or an ENUM in your database. 

This field type helps you narrow down the options for your admin users in a data set that you know will never change.

```
FormElement::search('name')
	->getInput()
	->setOptions(['Winter', 'Spring', 'Summer', 'Fall'])

FormElement::datalist('name')
	->getInput()
	->setOptions(['Winter', 'Spring', 'Summer', 'Fall'])
```
In the edit form, an admin user will be presented with a select box.


#### Date

The date and date range field types should be a DATE or DATETIME type in your database.

```
FormElement::date('name')
```

In the edit form, an admin user will be presented with a Datepicker.

#### File, Image

```
FormElement::image('name')
FormElement::file('name')
```

Files & Images are handled by `codesleeve/laravel-stapler` library.

This is the shortest way to attach an image object to a column:

```
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

