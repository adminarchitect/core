## Sortables

![Scopes](http://docs.adminarchitect.com/docs/images/index/sortables.jpg)

Any Admin Architect resource by default implements `Sortable` interface with single method `sortable()`.

It is smart enough to parse and analyze the resource model for any potentially "sortable" columns. Any indexed column is "sortable" by definition ...

To disable sorting by any column add this column into `unSortable` array in your Resource class:

```
protected $unSortable = ['id'];
```

To enable sorting by other columns, define `sortables` columns like so:

```
public function sortable()
{
	return [
		'id', 'email', 'name'
	];
}
```

To handle complex "sortable" column (for ex. relations, or columns from joined table) just define how to sort using Closure function:

```
public function sortable()
{
	return array_merge(
		# preserve auto-discovered
		$this->scaffoldSortable(),
		[
			'phone' => function ($query, $element, $direction) {
				return $query->join('user_contacts', function ($join) {
					$join->on('users.id', '=', 'user_contacts.user_id');
				})->orderBy("users_contacts.{$element}", $direction);
			}
		]
	);
}
```

Admin Architect also supports QueryBuilder class name notation:

```
public function sortable()
{
	return [
		'id', 'email', 'name',
		'skype' => SortUsersBySkype::class
	];
}
```
Where SortUsersBySkype is a simple class which implements Terranet\Administrator\Contract\QueryBuilder interface with single required method `build()`:

```
namespace App\Http\Terranet\Administrator\Queries;

use Terranet\Administrator\Contracts\QueryBuilder;

class SortUsersBySkype implements QueryBuilder
{
    private $query;

    private $element;

    private $direction;

    public function __construct($query, $element, $direction)
    {
        $this->query = $query;
        $this->element = $element;
        $this->direction = $direction;
    }

    public function build()
    {
        return $this->query->join('user_contacts', function ($join) {
            $join->on('users.id', '=', 'user_contacts.user_id');
        })->orderBy("users_contacts.{$this->element}", $this->direction);
    }
}
```