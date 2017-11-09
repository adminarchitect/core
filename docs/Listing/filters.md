## Overview

Filtering and listing resources is one of the most important tasks for administering a web application.

All of Admin Architect resources out of the box does support scopes & filters.

Admin Architect provides a set of default tools for you to build a compelling interface into your data for the admin staff.

### Filters

![Filters](http://docs.adminarchitect.com/images/index/filters.jpg)

Admin Architect provides a simple search implementation, allowing you to search/filter records in your application.

`Searchable` - become any indexed @varchar, @boolean, @date or @datetime columns.

Customize the set of filters, their types and even the way, how they `filter` data as you wish you need.

Let's update the default filters set by declaring new ones in our <Resource> class:

```php
# app\Http\Terranet\Administrator\Modules\<Resource>.php
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

	# optionaly for foreign columns (not existing in database, aggregated or joined, etc...) 
	# we can define a custom query
	->update('phone', function ($control) {
		$control->getInput()
			# when called, function will receive 2 arguments
			# 1. original query
			# 2. requested value
			->setQuery(function ($query, $value) {
				# created in Finder::getQuery()
				return $query 
						->join('user_profile as p', 'p.user_id', '=', 'users.id')
						->where('p.phone', $value);
			});

		return $control;
	});
}

protected function users()
{
    return ['' => '--Any--'] + User::pluck('name', 'id')->toArray();
}
```

Supported filter HTML types:
`text`, `select`, `datalist`, `date`, `daterange`, `search`, `number`.

As you might see, for complex filters that require more control while fetching resources, you are able to define an optional \Closure $query attribute via setQuery() method.

To disable filters for specific resource - remove Filtrable interface from Resource's `implements` section or just return an empty collection:

### Scopes

![Scopes](http://docs.adminarchitect.com/images/index/scopes.jpg)

As like as for filters feature, if Resource implements interface `Filtrable` it will parse the Eloquent model for any available scopes (having no dynamic arguments).

Use scopes to create sections of mutually exclusive resources for quick navigation and reporting.

This will add a `tab` bar above the index table to quickly filter your collection on pre-defined scopes.

In addition, if your model implements `SoftDeletes` contract, useful scopes like `withTrashed`, `onlyTrashed` will be available too.

To hide a scope just add it to Resource `$hiddenScopes` array:

```php
protected $hiddenScopes = ['active'];
```

If for some reason you don't want to create new Model's scope you are able to define isolated, Resource-related scopes, like so:

```php
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

_Hint: Few different ways to add a scope._

```php
# Queryable class (should have query() method)
(new Scope('name'))->setQuery(Queryable::class);

# Class@method syntax
(new Scope('name'))->setQuery("User@active");

# Standard, Closure style
(new Scope('name'))->setQuery(function($query) { return $this->modify(); });

# Callable instance
(new Scope('name'))->setQuery([SomeClass::class, "queryMethod"]);
```
