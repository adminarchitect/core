## Overview

Filtering and listing resources is one of the most important tasks for administering a web application.

All AdminArchitect resources out of the box does support scopes & filters.

Admin Architect provides a set of default tools for you to build a compelling interface into your data for the admin staff.

### Filters

![Admin Architect - Filters](http://docs.adminarchitect.com/images/index/filters.jpg)

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
	->except(['column 1', 'column x'])

	# let's filter our collection by user_id column
	->push(
		Enum::select('user_id', [], ['' => '--Any--'] + User::pluck('name', 'id'))
	)

	# optionaly for foreign columns (not existing in database, aggregated or joined, etc...) 
	# we can define a custom query
	->push(Text::make('Phone')->setQuery(function (Builder $builder, $value) {
		return $builder
			->join('place_details as pd', 'pd.place_id', '=', 'places.id')
			->where('phone', $value);
	}));
}
```

Supported filters can be found in `Terranet\Administrator\Filter` location:

As you might see, for complex filters that require more control while fetching resources, you are able to define an optional `\Closure` $query attribute via `setQuery()` method.

To disable filters for specific resource - remove Filtrable interface from Resource's `implements` section or just return an empty collection:

## Scopes

![Admin Architect - Scopes](http://docs.adminarchitect.com/images/index/scopes.jpg)

AdminArchitect uses scopes to create sections of mutually exclusive resources for quick navigation and reporting.

This will add a `tab` bar above the index table to quickly filter your collection on pre-defined scopes.

If you want an Eloquent scope to be a part of AdminArchitect scopes, just annotate it using built-in `@ScopeFilter` annotation.

```php
/**
 * @ScopeFilter()
 * @param  Builder  $query
 * @return Builder
 */
public function scopeActive(Builder $query)
{
	return $query->where('active', true);
}

/**
 * @ScopeFilter()
 * @param  Builder  $query
 * @return Builder
 */
public function scopeInactive(Builder $query)
{
	return $query->where('active', false);
}

/**
 * @ScopeFilter(name="Callable", icon="fa-phone")
 * @param  Builder  $query
 * @return Builder|\Illuminate\Database\Query\Builder
 */
public function scopeHavingPhone(Builder $query)
{
	return $query->join('place_details as pd', 'pd.place_id', '=', 'places.id')
		->whereNotNull('pd.phone');
}
```

In addition, if your model implements `SoftDeletes` contract, useful scopes like `withTrashed`, `onlyTrashed` will be available too.

If for some reason you don't want to create new Model's scope you are able to define isolated, Resource-related scopes, like so:

```php
public function scopes()
{
	return $this->scaffoldScopes()->push(
		(new Scope('name'))->setQuery(function($query) { 
			$query->where('updated_at', '>=', Carbon::today()->subWeek());
		});
	);
}
```
