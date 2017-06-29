## Finders

Every single Resource uses as called `Finder` service to fetch the model's items.

For simple resources it is more then enough, but, there are a lot of cases when you'll need your custom way to fetch the items.

### Create a finder

To define a new way to find items, create a Finder.
Note! for simplicity follow the same naming convention - Give the same name as for Resource.

Let's say for our users Resource we need a different results.

```
php artisan administrator:finder Users
```

Finder provides 2 important methods you migth wish to overwrite:

* fetchAll() - finds all items, according with Scopes & Filters sets.
* find() - finds a single item when it is going to be Edited or Deleted, etc...

Note! instead of overwriting `fetchAll()` method, we recommend to overwrite the `getQuery()` method, which is used in different parts of Admin Architect, like Exportable collections.

so, to complete our example:

```
protected function getQuery()
{
	return parent::getQuery()
		->where('role', 'admin');
}
```

Now, the final collection of users will contain only Administrators.