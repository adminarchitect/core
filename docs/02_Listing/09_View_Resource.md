## Show Resource Page

![Scopes](http://docs.adminarchitect.com/docs/images/index/presenters.jpg)

Every single row in your resource is available for preview out of the box.

In many cases models have a bunch of relationships with other models in the system. So why don't link them altogether and view on the single page?


```
class User extends Model
{
	protected $fillable = ['id', 'name', 'email', '...'];

	/**
	 * Record relationship
	 *
	 * @widget
	 */
	public function record()
	{
		return $this->hasOne(App\Record::class);
	}

	/**
	 * Article relationship
	 *
	 * @widget
	 * @placement main-bottom
	 */
	public function articles()
	{
		return $this->hasMany(App\Article::class);
	}
}

class Record extends Model
{
	protected $fillable	= ['target', 'height', 'current_weight', 'target_weight'];
}

class Articles extends Model
{
	protected $fillable = ['title', 'body', 'published'];
}
```

these relations will be automatically rendered at the view User page as widgets.

sure, you can control the way how they rendered by defining:

* @placement - one of the: model, sidebar, main-top, main-bottom
* @tab - Admin Architect will create a tab for this widget (can be used in conjunction with @placement flat)
* @order - arrange widgets by order


As a bonus you can add as many Resource-scoped widgets as you like by listing them in Resource class:

```
public function widgets()
{
	$user = app('scaffold.model');

	return array_merge(
		$this->scaffoldWidgets(),
		[
			new UserStats($user)
		]
	);
}
```

where UserStats is a simple `Widgetable` class:

```
class UserStats extends AbstractWidget implements Widgetable
{
    protected $user;

    protected $total;

    protected $published;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Widget contents
     *
     * @return mixed
     */
    public function render()
    {
        return view('admin.users.stats')->with([
            'total'     => $this->total(),
            'published' => $this->published(),
            'draft'     => $this->draft(),
            'user'      => $this->user
        ]);
    }

    private function total()
    {
        if (null === $this->total) {
            $this->total = $this->user->articles()->count();
        }

        return $this->total;
    }

    private function published()
    {
        if (null === $this->published) {
            $this->published = $this->user->articles()->wherePublished(1)->count();
        }

        return $this->published;
    }

    private function draft()
    {
        return $this->total() - $this->published();
    }
}
```
![Scopes](http://docs.adminarchitect.com/docs/images/index/view_widgets.jpg)