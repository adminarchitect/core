## Templates

Sometimes, while developing complex applications, you'll need to change the default layout or partial view: Maybe you'll want to inline javascript, of add dynamic behavior for index page, or your edit/create form requires custom javascript libraries for more customization...

Admin Architect comes with a solution called `Template`: All rendered pages are separated in a `replaceable` blocks.

Template is registered per Resource, so if you want to customise your `index` view for `Users` resource, just create new template:

```bash
php artisan administrator:template Users
```

```php
class Users extends Template implements TemplateProvider
{
    /**
     * Scaffold index templates
     *
     * @param $partial
     * @return mixed array|string
     */
    public function index($partial = 'index')
    {
        $partials = array_merge(
            parent::index(null),
            [
                'index' => 'admin.users.custom_index'
            ]
        );

        return (null === $partial ? $partials : $partials[$partial]);
    }
}
```

Admin Architect will search for a view `resources/views/admin/users/custom_index.blade.php` for index screen.

For more details please checkout the Template class: `Terranet\Administrator\Services\Template`;
