## Configuration

Admin Architect needs almost no other configuration out of the box.
You are free to get started developing!

However, you may wish to review the `config/administrator.php` file and its contents. It contains several options that you might wish to change according to your application.

Let's review some of them:
```php
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

```php
'conditions' => [
	'role' => 'admin'
],
```

#### User::isSuperAdmin

```php
'permission' => \Terranet\Administrator\Auth\SuperAdminRule::class,
```

By default AdminArchitect uses a convention that only user with `id === 1` is a super admin. You can change this approach by adding a method to your 'auth.model' class, called isSuperAdmin().

Ex.: based on previous example with `role`, your User::isSuperAdmin() might look like:

```php
public function isSuperAdmin()
{
	return $this->role === 'admin';
}
```
or based on some 3rd party ACL packages, like `zizaco/entrust`:
```php
public function isSuperAdmin()
{
	return $this->hasRole('admin');
}
```

#### Paths
All Admin Architect related services, actions, savers, finders will be saved in these directories.
```php
'paths' => [
	...
]
```

#### Passwords Manager
```php
'manage_passwords' => true
```
Out of the box for admin area, Admin Architect listens for `User::saving()` event to handle passwords in a right way - in other words, converts plain text to a hash.

If you find this feature unnecessary - just set it to `false`.

#### File Manager
```php
'file_manager' => false,
```
Enable File Manager by setting its option to `true`;

#### Factories
There are few factories (containers) you might be interested in.

```php
# Navigation factory
'menu' => \Terranet\Administrator\Navigation\Factory::class,

# Dashboard panels factory
'dashboard' => \App\Http\Terranet\Administrator\Dashboard\Factory::class,
```