## Export Collections

![Scopes](http://docs.adminarchitect.com/docs/images/index/exports.jpg)

Any Resource that implements `Exportable` interface allows you to export collections to a different formats.

Provided out of the box are: XML, JSON, CSV

There is an easy way to customise the set of available formats:

* To register new formats define `formats()` method in your resource, like this:

```php
public function formats()
{
	return array_push(
		$this->scaffoldFormats(),
		['pdf']
	);
}
```
* To control global or custom formats per each resource, there is a special section in `config/administrator.php`, called `exports`.

```php
...
'export' => [
    'default' => ['xml', 'csv', 'json'],  # default formats list
    // 'users' => ['csv', 'pdf'], # users resource will have only csv & pdf formats
],
...
```

### Export to a new format
To allow exporting to a new format, just add format exporter by declaring `to<Format>` method:

```php
public function toPdf($query)
{
	$pdf = 'code that exports collection';

	return response($pdf, 200, ['Content-Type' => 'application/pdf']);
}
```

*Note:* Probably you'll need to use a 3rd party PDF rendering library to get the PDF Builder working as you wish.

### Exportable Columns & Query

Sometimes you need to control the query & columns that are going to be exported.
For these purposes you have to extend/rewrite 2 methods:

```php
protected function exportableColumns(): array
{
	return collect(parent::exportableColumns())
            ->diff(['offers.admin_id', 'offers.company_id', 'offers.place_id']])
            ->merge([
                'a.name as Author',
                'c.name as Company',
                DB::raW("CONCAT_WS(', ', pt.name, ct.name) AS location"),
                DB::raw("IF(1 = priority, 'yes', 'no') as priority"),
                DB::raw("IF(1 = active, 'yes', 'no') as active"),
                DB::raw("industry.name as Industry"),
                DB::raw("job_type.name as JobType"),
            ])
            ->all();
}

protected function exportableQuery(Builder $query): Builder
{
    return parent::exportableQuery($query)
                 ->join('admins as a', 'a.id', '=', 'offers.admin_id')
                 ->join('companies as c', 'c.id', '=', 'offers.company_id')
                 ->withCount('users')
                 ->when(true, function ($query) {
                     $query = Tag::fetchOrigin($query, 'industry');
                     $query = Tag::fetchOrigin($query, 'job_type');

                     return $query;
                 })
                 ->when(true, function ($query) {
                     return Place::fetchLocation($query, 'offers.place_id');
                 });
}
```
_ Note: Add these methods to your Resource or Actions Service class. _
