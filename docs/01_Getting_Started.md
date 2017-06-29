## Admin Architect

Admin Architect is a framework for creating administration interfaces.
It abstracts common business application patterns to make it simple for developers to implement beautiful and elegant interfaces with very little effort.


# Installation

Downlad the [latest](http://codecanyon.net/item/laravel-admin-administration-framework/13528564) version of Admin Architect and extract this archive anywere in your project, for instance ```packages``` directory.

Add a new repository to your composer.json

```
"repositories": [
  ...
  {
    "type": "git",
    "url": "./packages/administrator"
  }
  ...
]
```

Since our repository type is git, let's init a new repository

Enter to ```packages/administrator``` directory and run:

```
cd ./packages/administrator;
git init;
git add .
git commit -m 'First init'
```

Let's install composer package:

```
composer require terranet/administrator
```

Once the package installed, register its service provider in config/app.php file.

```
'providers' => [
    ...
    Terranet\Administrator\ServiceProvider::class,
	...
]
```

Publish package's assets, translation and configuration by running:

```
artisan vendor:publish --provider=Terranet\\Administrator\\ServiceProvider
```

## Migrations
if you're running a fresh Laravel installation, run:

```
php artisan migrate
```

## Create administrator

Now let's create a new administrator:

```
artisan administrator:create
```

Admin Architect needs almost no other configuration out of the box.
You are free to get started developing!

However, you may wish to review the config/administrator.php file and its contents. It contains several options that you might wish to change according to your application.

Let's review some of them:

```
# authentication options
'auth' => [
	'identity' => 'email',
	'credential' => 'password',
	'model' => \App\User::class,
	'conditions' => null,
],
```
* If you want to use different model to authenticate your users (for inst.: \App\Admin) - you can change it here.

* Or maybe the main identity you want to use is `username` rather than `email`.

* If there is a case to limit users who can login to admin by adding some additional login criterias - use `conditions`. So for instance the only users who have specific `role` in your database can login, your `conditions` might look like:

```
	...
	'conditions' => [
		'role' => 'admin'
	]
	...
```

#### User::isSuperAdmin

```
'permission' => \Terranet\Administrator\Auth\SuperAdminRule::class,
```

By default AdminArchitect uses a convention that only user with `id === 1` is a super admin. You can change this approach by adding a method to your 'auth.model' class, called isSuperAdmin().

Ex.: based on previous example with `role`, your User::isSuperAdmin() might look like:
```
public function isSuperAdmin()
{
	return $this->role === 'admin';
}
```

or based on some 3rd party ACL packages, like `zizaco/entrust`:

```
public function isSuperAdmin()
{
	return $this->hasRole('admin');
}
```

#### Paths

All Admin Architect related services, actions, savers, finders will be saved in these directories.
```
'paths' => [
	...
]
```

#### Passwords Manager

```
'manage_passwords' => true
```

Out of the box for admin area, Admin Architect listens for User::saving() event to handle passwords in a right way - in other words, converts plain text to a hash.

If you find this feature unnecessary - just set it to `false`.

#### Factories

```
# Navigation factory
'menu' => \Terranet\Administrator\Navigation\Factory::class,

# Dashboard factory
'dashboard' => \App\Http\Terranet\Administrator\Dashboard\Factory::class,
```
