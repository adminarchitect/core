## Installation

As you always do, install composer package:

```bash
composer require adminarchitect/core
```

### Registering 
_[Skip for Laravel 5.5]_

Once the package installed, register its service provider in config/app.php file.

```php
'providers' => [
    ...
    Terranet\Administrator\ServiceProvider::class,
	...
]
```
### Publishing

Publish package's assets, translation and configuration by running:

```bash
php artisan administrator:publish
```
Answer few questions about configuration steps.
It will copy adminarchitect's assets to a resources/assets/administrator directory.
All views will be copied to a resources/views/vendor/administrator directory.

### Assets
AdminArchitect assets are provided in ES6 and less/sass formats, so to convert them to a js/css there is a NPM package `@adminarchitect/mix`.

```bash
yarn -D @adminarchitect/mix
OR
mpm i @adminarchitect/mix --save-dev
```

Next step is to register AdminMix tasks, so add these lines to your `webpack.mix.js`:

```js
const AdminMix = require('@adminarchitect/mix');
(new AdminMix).handle();
```

then you can run any of these commands, to build assets:
```bash
yarn run [dev|production|watch]
```

All generated assets will be placed to `public/admin` directory.

### Migrations
if you're running a fresh Laravel installation, run:

```bash
php artisan migrate
```

### Create administrator

Now let's create a new administrator account:

```bash
php artisan administrator:create
```

### Enjoy

Now you can access the Admin Architect by opening a `/cms` url. 
So for `php artisan serve` command, it will: [http://localhost:8000/cms](http://localhost:8000/cms)