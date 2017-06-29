## Filters & Scopes

Filtering and listing resources is one of the most important tasks for administering a web application.

All of Admin Architect resources out of the box does support scopes & filters.

Admin Architect provides a set of default tools for you to build a compelling interface into your data for the admin staff.

### Filters

![Filters](http://docs.adminarchitect.com/docs/images/index/filters.jpg)

Admin Architect provides a simple search implementation, allowing you to search/filter records in your application.

*searchable* - become any indexed @varchar, @boolean, @date or @datetime columns.

Customize the set of filters, their types and even the way, how they `filter` data as you wish you need.

Let's update the default filters set by declaring new ones in our <Resource> class:

```
public function filters()
{
    return $this
	# Preserve auto-discovered filters
	->scaffoldFilters()

	# optionaly remove unnecessary
	->without(['column 1', 'column x'])

	# let's filter our collection by user_id column
	->push(
		FilterElement::select('user_id', [], $this->users())
	)

	# optionaly for foreign columns we can define a custom query
	->update('user_id', function ($userId) {
		$userId
			->getInput()
			->setQuery(function ($query, $value) {
				return $query->whereIn('user_id', [$value]);
			});

		return $userId;
	});
}

protected function users()
{
    return ['' => '--Any--'] + User::pluck('name', 'id')->toArray();
}
```

Supported filter types are:
`text`, `select`, `date`, `daterange`, `search`, `number`.

As you might see, for complex filters that require more control when fetching resources, you are able to define an optional \Closure $query attribute via setQuery() method.

To disable filters for specific resource - remove Filtrable interface from Resource's `implements` section or just return an empty collection:

### Scopes

![Scopes](http://docs.adminarchitect.com/docs/images/index/scopes.jpg)

As like as for filters feature, if Resource implements interface `Filtrable` it will parse Resource model for any available scopes.

Use scopes to create sections of mutually exclusive resources for quick navigation and reporting.

This will add a `tab` bar above the index table to quickly filter your collection on pre-defined scopes.

In addition, if your model implements `SoftDeletes` contract some of useful scopes (like `withTrashed`, `onlyTrashed`) will be available too.

To hide a scope just add it to Resource `$hiddenScopes` array:

```
protected $hiddenScopes = ['active'];
```

If for some reason you don't want to create new Model's scope you are able to define isolated, Resource-related scopes, like so:

```
public function scopes()
{
    return $this->scaffoldScopes()
        ->push(
            (new Scope('active'))
				# Like columns and hints, scopes are auto-translatable
				# parsed keys are:
				# 1. administrator::scopes.<module>.<scope_id>
				# 2. administrator::scopes.global.<scope_id>
				# but you always can use this method like so:
				->setTitle('Scope title')
				->setQuery(function ($query) {
					return $query->whereActive(1);
				})
        );
}
```
