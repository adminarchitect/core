## Exportable collections

![Scopes](http://docs.adminarchitect.com/docs/images/index/exports.jpg)

Any Resource that implements `Exportable` interface allows you to export collections to a different formats.

Provided out of the box are: XML, JSON, CSV

There is an easy way to customise the set of available formats:

In your resource add a method:

```
public function formats()
{
	return array_push(
		$this->scaffoldFormats(),
		['pdf']
	);
}
```

and add format exporter by declaring `to<Format>` method:

```
public function toPdf($query)
{
	$pdf = 'code that exports collection';

	return response($pdf, 200, ['Content-Type' => 'application/pdf']);
}
```

*Note:* Probably you'll need to use a 3rd party PDF rendering library to get the PDF Builder working as you wish.


