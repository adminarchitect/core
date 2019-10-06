# Installation

As you always do, install composer package:

```bash
composer require adminarchitect/core
```

## Registering 
_[Skip for Laravel 5.5+]_

Once the package installed, register its service provider in config/app.php file.

```php
'providers' => [
    ...
    Terranet\Administrator\ServiceProvider::class,
	...
]
```

## Publishing

Publish package's assets, translation and configuration by running:

```bash
php artisan administrator:publish
```

Answer few questions about configuration steps.
It will copy adminarchitect's assets to a `<project-dir>/administrator-mix` directory.
Views & translations files will be copied to a corresponding locations: `resources/{views,lang}/vendor/administrator` directories.

## Assets
AdminArchitect assets are provided in ES6 and less/sass formats, so to convert them to a js/css, enter the `adminarchitect-mix` directory and run:

```bash
npm i
mpm run [dev|production|watch]
```

## Editors

AdminArchitect comes with 4 visual editors: TinyMce, CkEditor, Medium & Markdown.
In order to connect editor of your choice - follow the instructions from `adminarchitect-mix/webpack.mix.js` file.

then you can run any of these commands, to build assets:

All generated assets will be placed to `public/admin` directory.

## Migrations

if you're running a fresh Laravel installation, run:

```bash
php artisan migrate
```

## Create administrator

Now let's create a new administrator account:

```bash
php artisan administrator:create
```

## Enjoy

Now you can access the Admin Architect by opening a `/cms` url. 
So for `php artisan serve` command, it will: [http://localhost:8000/cms](http://localhost:8000/cms)