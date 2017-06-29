## Index Screen

Admin Architect makes any resource available by default for listing.
Any `$fillable`, `indexed` and `$dates` columns will be available for listing by default.

Adding new column is simple, just let the Admin Architect to know what to show and how.

Note: .dot notation also works while referencing the relationship columns.

In many cases you will probably leave resources untouched, but sometimes complex resources needs customization.


### Columns customization

![Customizing Columns](http://docs.adminarchitect.com/docs/images/index/columns.jpg)

Admin Architect will propose the default (auto-discovered) set of columns for publishing at index screen.

You can always change the default look & feel by customizing any column in a set.

To do so you need to overwrite `<Resource>::columns()` method.


```
public function columns()
{
    return

	# preserve auto-discovered columns
	$this
	->scaffoldColumns()
	# remove unnecessary columns
	->without(['dates', 'some_other_column'])

	# add a new element
	->push(new \Terranet\Administrator\Columns\Element('note'))

	# move element to a position
	->move('note', 'after:body')

	# Add a group of new elements
	->group('meta', function (Group $group) {
		# Note that .dot notation means relationship, like $post->meta->keywords
		$group->push(new Element('meta.keywords'));
		$group->push(new Element('meta.description'));
	})

	# Join existing elements to a group
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
		$element->setTitle('My name is Element');

		# change input type to a dropdown
		$element->setInput(
			(new Select('someElement'))->setOptions(['dropdown', 'options'])
		)

		return $element;
	});
}
```

Note: `scaffoldColumns()` method returns a `Mutable` collection extended with some useful methods, like:

* push($element) - insert new element
* insert($element, $position) - insert new element to a position
* without($element) -> remove element
* move($element, $position) -> move element, position: integer|before:target|after:target
* moveBefore($element, $target)
* moveAfter($element, $target)
* update($element, $callback) - update element
* updateMany(array $elements) - update many elements at once
* group($id, Closure $callback) -> create a new group of elements
* join(array $elements, $id, $position) - join existing elements into a group
* find($element) - find an element

### Presenters

![Presenters](http://docs.adminarchitect.com/docs/images/index/presenters.jpg)

Customizing each column is a good idea, but it can by annoying for complex resources, also it leaves untouched the View Resource & Relations pages.

There is a recommended way to customise resources in Admin Architect, called `Presenters`;

`Presenter` - presentable package included in Admin Architect.

let's `Present` our `Post` model.

```
/** @class App\Post */
class Post extends Model implements \Terranet\Presentable\PresentableInterface
{
    use \Terranet\Presentable\PresentableTrait;

    protected $fillable = ['user_id', 'active'];

    protected $presenter = PostPresenter::class;
}

/** @class App\Presenters\PostPresenter */
class PostPresenter extends Presenter
{
    public function title()
    {
        return link_to_route('scaffold.view', $this->presentable->title, ['module' => 'posts', 'id' => $this->presentable]);
    }

    public function body()
    {
        return '<p class="text-muted">' . $this->presentable->body . '</p>';
    }

    public function adminActive()
    {
        return \admin\output\boolean($this->presentable->active);
    }
}
```

*Note: if you don't want to mix up your `backend` and `frontend` presenters, just prefix presenter method with `admin` word, so:*

your `title()` method will be transformed to `adminTitle()`.

This way you can use simple `title()` method in a front views, when
`adminTitle` will be used by Admin Architect.

Now, when AdminArchitect will ask for element called `title` it will use your presenter's `adminTitle()`.
