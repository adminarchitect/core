## Index Screen

Admin Architect makes any resource available by default for listing.
Any `$fillable`, `$translatedAttributes`, `indexed` and `$dates` columns will be available for listing by default.
Adding new column is simple, just let the Admin Architect to know what to render and how.
In many cases you will probably leave resources untouched, but sometimes complex resources needs customization.

_[Note]: .dot notation also works while referencing the relationship columns._

### Grid

![Admin Architect - Columns Grid](http://docs.adminarchitect.com/images/index/columns.jpg)

Admin Architect will propose the default (auto-discovered) set of columns for publishing at index screen.
You can always change the default look & feel by customizing any column in a set.
To do so you need to overwrite `<Resource>::columns()` method.

```php
public function columns()
{
    return

	# preserve auto-discovered columns
	$this
	->scaffoldColumns()
	# remove unnecessary columns
	->without(['dates', 'some_other_column'])

	# add a new table column element
	->push('note')

	# add a HasOne or BelongsTo relationship column (Ex.: $eloquent->profile->phone)
	->push('profile.phone')

	# if you want to controle the ouput, the callback 
	# function is available as second argument.
	->push('profile.phone', function(Element $element) {
		# custom output goes here...
		return '<a href="#">.....</a>'; 
		return view('path.to.view')->with([...$args]);
	})

	# Assuming that comments is a HasMany or ManyToMany relationship, the
	# $eloquent->comments->count() will be inserted into a comments column
	->insert('comments', 'after:note') 

	# move element to a position
	->move('note', 'after:body')

	# Add a group of new elements
	->group('meta', function (Group $group) {
		# Note that .dot notation means relationship, like $post->meta->keywords
		$group->push('meta.keywords');
		$group->push('meta.description');
	})

	# or Join existing elements to a group
	->join(['meta.title', 'meta.description', 'meta.keywords'], 'meta')

	# update a group
	->update('meta', function ($group) {
		return $group->move('meta.title', 'before:meta.description');
	})

	# update an element
	->update('someElement', function ($element) {
		# Note: by default AdminArchitect will try to discover
		# translation for each column by asking a Translator for
		# a key: administrator::columns.<module>.<column>,
		# then if not found: administrator::columns.general.<column>,
		# so setting a title might be redundant.
		$element->setTitle('The element name');

		# the alternative way is to add the following lines to a `columns.php` file.
		'<resource_url>' => [
			...
			'someElement' => 'The element name'
			...
		],
		

		# change input type to a dropdown
		$element->setInput(
			(new Select('someElement'))->setOptions(['dropdown', 'options'])
		)

		return $element;
	});
}
```

Note: `scaffoldColumns()` method returns a `Mutable` collection extended with some useful methods, like:

* push($element, \Closure $callback = null) - insert new element to the end of collection
* insert($element, $position, \Closure $callback = null) - insert new element to a position (integer, 'before:target', 'after:targer' values accepted)
* without($element) -> remove element[s]
* move($element, $position) -> move element, position: integer|before:target|after:target
* moveBefore($element, $target)
* moveAfter($element, $target)
* update($element, $callback) - update an element
* updateMany(array $elements) - update many elements at once
* group($id, Closure $callback) -> create a new group of elements
* join(array $elements, $id, $position) - join existing elements into a group
* find($element) - find an element
