# Savers

Admin Architect does a huge work to persist your model, presented by a Form.

It also handles Images, Files, RelationShips, Translations, Media, etc...

But somethimes it is not enough, you need a way to store your form data differently.

For these cases we provide a Resource-dedicated service called: `Saver`

## Create saver

Let's store our users differently

```bash
php artisan administrator:saver Users
```

The Savers are stored in a `app\Http\Terranet\Administrator\Savers` directory
There is one single public method `sync()` and a bunch of protected methods you might wish to verwrite.

Let's say, we need to create a log record, once a User were saved:

Note! Yes, we know, there is a better way to do it (using events, queued jobs, etc...), but just for a demonstration purpose, let's do it this way...

```php
public function sync()
{
	# preserve parent functionality
	parent::sync();

	$admin = auth('admin')->user();

	UserLog::create([
		'edited_by' => $admin->id,
		'action' => 'save',
		'data' => $this->request->all()
	]);

	$this->repository->editors()->attach(
		$admin
	);
}
```