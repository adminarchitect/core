## Navigation

By default any new resource is auto Navigable.

To change the way how it appears in the navigation there is a set of methods provided out of the box, like:

#### Resource title

```php
public function title()
{
	return "Articles";
}

public function singular()
{
	return "Article";
}
```

#### Group resources in sub-menus by returing a common group name

```php
public function group()
{
	return "Content"
}
```

#### Define when to show or hide your resource from navigation.

```php
public function showIf()
{
	return auth('admin')->hasRole('manager');
}
```

#### Define the order
*Hint: set `ordering` => true in config/menus.php*

```php
public function order()
{
	return 1;
}
```

#### Change the resource url

```
public function url()
{
	return "articles";
}
```

#### Define which navigation to use
There are 2 navigation types available in Admin Architect:
*Navigable::MENU_SIDEBAR &amp; Navigable::MENU_TOOLS*


```
public function navigableIn()
{
	return Navigable::MENU_TOOLS;
}
```

#### Sidebar

![Sidebar](http://docs.adminarchitect.com/docs/images/navigation/sidebar.jpg)

#### Tools menu
![Tools](http://docs.adminarchitect.com/docs/images/navigation/tools.jpg)


#### Navigation link attributes
Make navigation beautiful by assigning amazing icons [font-awesome or ion icons available]:

```php
public function linkAttributes()
{
    return ['icon' => 'fa fa-circle-o', 'id' => $this->url()];
}
```

## Custom navigation

To build navigations, Admin Architect uses https://github.com/pingpong-labs/menus.

Any resource that implements `Navigable` contract will be displayed in the sidebar navigation by default.

To disable the resource from being displayed in the global navigation just don't implement that interface.

For more details about how to customize the resource appearance in the navigation please checkout the [Resources Navigation](http://docs.adminarchitect.com/Resources) documentation section.

To change default navigation structure, checkout `App\Http\Terranet\Administrator\Navigation` class.

There is a navigation skeleton you might customise for your needs:

```php
protected function makeSidebar()
{
	$this->navigation->create(Navigable::MENU_SIDEBAR, function (MenuBuilder $sidebar) {
		// Dashboard
		$sidebar->route('scaffold.dashboard', trans('administrator::module.dashboard'), [], 1, [
			'id' => 'dashboard',
			'icon' => 'fa fa-dashboard',
		]);

		// Create new users group
		$sidebar->dropdown(trans('administrator::module.groups.users'), function (MenuItem $sub) {
			if (auth('admin')->user()->canAdmin('admin.users.create')) {
				$sub->route(
					'scaffold.create',
					trans('administrator::buttons.create_item', ['resource' => 'User']),
					['module' => 'users'], 1, []
				);
			}
		}, 2, ['id' => 'groups', 'icon' => 'fa fa-group']);
	});
}

protected function makeTools()
{
	$this->navigation->create(Navigable::MENU_TOOLS, function (MenuBuilder $tools) {
		$tools->url(
			route('scaffold.logout'),
			trans('administrator::buttons.logout'),
			100,
			['icon' => 'glyphicon glyphicon-log-out']
		);
	});
}
```