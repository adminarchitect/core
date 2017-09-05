## Finder

Every single Resource uses a class, called `Finder` - service that fetches (finds) the model's item(s).

For simple resources it is more then enough, but, there are a lot of cases when you'll need your custom way to fetch the items.

### Create a finder

To define a new way to find items, create a custom `Finder`.

_Note! for simplicity follow the same naming convention - Give the same name as for Resource._

Let's say for our users Resource we need a different results.

```bash
php artisan administrator:finder Users
```

Finder provides 2 important methods you migth wish to overwrite:

* `fetchAll()` - finds all items, according with Scopes & Filters sets.
* `find()` - finds a single item when it is going to be Edited or Deleted, etc...

Note! instead of overwriting `fetchAll()` method, we recommend to overwrite the `getQuery()` method, which is used in different parts of Admin Architect, like Exportable collections.
so, to complete our example:

```php
# let's assume we need only active users in our grid
protected function getQuery()
{
	# calling the parent::getQuery() method is crucial part here
	# because it includes assembling of Scopes, Filters, Sortables, etc...
	return parent::getQuery()->whereNull('locked_at');
}
```

Now, the resulted collection of users will contain only active users.