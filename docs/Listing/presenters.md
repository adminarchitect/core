## Presenters

![Admin Architect - Presenters](http://docs.adminarchitect.com/images/index/presenters.jpg)

Customizing every single column in a `columns()` method is a good idea, but it can by annoying for complex resources, also it leaves untouched the View Resource & Relations pages.

There is a recommended way to customise resources in Admin Architect, called `Presenters`;

`Presenter` - presentable package included in Admin Architect out of the box.

Let's `Present` our `Post` model.

```php
/** @class App\Post */
class Post extends Model implements \Terranet\Presentable\PresentableInterface
{
    use \Terranet\Presentable\PresentableTrait;

    protected $fillable = ['user_id', 'active'];

    protected $presenter = PostPresenter::class;
}

/** @class App\Presenters\PostPresenter */
class PostPresenter extends Terranet\Presentable\Presenter
{
    public function title()
    {
        return link_to_route('scaffold.view', $this->presentable->title, [
        	'module' => 'posts', 
        	'id' => $this->presentable
    	]);
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

*Note: Presenter is a common pattern, so if for some reason you don't like to mix up your `backend` and `frontend` presenters, just prefix presenter method with `admin` word, so:*

rename `title()` method to `adminTitle()`.

This way you can use simple `title()` method in a front views, when
`adminTitle()` will be used by Admin Architect.

Now, when AdminArchitect asks for element called `title` the presenter's `adminTitle()` will be executed instead.
