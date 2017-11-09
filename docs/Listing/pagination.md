## Pagination

Like any other administration framework, Admin Architect provides a simple way to split big collections to pages.

![Scopes](http://docs.adminarchitect.com/images/index/pagination.jpg)

You can set the number of records fetched by default per resources:

Default pagination perPage value is 20.

if you want to change this value just rewrite `perPage` method in your Resource class.

```
public function perPage()
{
	return 10;
}
```