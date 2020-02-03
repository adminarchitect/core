## Forms

![Admin Architect - Form](http://docs.adminarchitect.com/images/form/edit.jpg)

Admin Architect gives you a complete control over the output of the form by creating a thin DSL on top of Illuminate\Form package:

Defining of all fields for editing can by annoying, so Admin Architect eliminates that step: all `$fillable` and `$translatedAttributes` (mulilingual support) columns are available for editing.

You can extend default form by adding columns or changing the column settings:

For models, each field should be one of your model's SQL columns or one of its Eloquent relationships or any custom field (assuming you define the mechanism to handle and save it).

```php
public function form()
{
    return $this->scaffoldForm()
        ->update('description', function (TranslatableField $field) {
            return $field->tinymce();
        })
        ->push(CustomField::make('custom_view'))
        ->push(HasOne::make('Place Details', 'details'))
        ->push(BelongsTo::make('Belongs to City', 'city')->searchable(false))
        ->push(BelongsToMany::make('Belongs to Tags', 'tags')->tagList())
        ;
}
```
=
### Supported form controls

For complete list of supported fields please review the `Terranet\Administrator\Field` directory:


### Files & Images
Files & Images are handled by `czim/laravel-paperclip` library.

This is the shortest way to attach an image object to a column:

```php
class User extends Eloquent implements \Czim\Paperclip\Contracts\AttachableInterface
{
    use \Czim\Paperclip\Model\PaperclipTrait;

    # Add the 'avatar' attachment to the fillable array,
	# so that it's mass-assignable on this model.
    protected $fillable = ['name', 'email', 'image'];

    public function __construct(array $attributes = array())
    {
        $this->hasAttachedFile('image', [
            'variants' => [
                'medium' => [
                    'auto-orient' => [],
                    'resize'      => ['dimensions' => '300x300'],
                ],
                'thumb' => '100x100',
            ],
            'attributes' => [
                'variants' => true,
            ],
		]);
		
        parent::__construct($attributes);
    }
}
```
For more info please checkout its documentation by accessing https://github.com/czim/laravel-paperclip.

### Media collection

![Admin Architect - Media](http://docs.adminarchitect.com/images/form/media.png)

Sometimes you may need to have multiple images attached to an Eloquent model.
Let's say your User model should have many Images.
In Admin Architect adding a `media` collection is quite easy:
Admin Architect uses [Laravel MediaLibrary](https://github.com/spatie/laravel-medialibrary) package.
So the very first thing you have to do - configure your model to use Media Library.
Then in your resource you can call method `media` to add a `media` control to edit form. 

```php
# Ex.: app/Http/Terranet/Administrator/Modules/Users.php

// if you want to view the status on index page
public function columns(): Mutable
{
    return $this->scaffoldColumns()
        ->push(Media::make('images'))
        ;
}

// to allow adding media files on Item Detail page
public function viewColumns(): Mutable
{
    return $this->scaffoldColumns()
        ->push(Media::make('images'))
        ;
}
```

P.S. you can use the same `media()` method when listing/viewing models `<Resource>::columns()` & `<Resource>::viewColumns()`.