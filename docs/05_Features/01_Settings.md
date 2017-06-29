## Settings

* *requires terranet/options package*

![Overview](http://docs.adminarchitect.com/docs/images/plugins/settings.jpg)

### Installation

In order to support settings within Admin Architect interface you'll need to install the `terranet/options` package (delivered separately).

Note: After installing it be sure the `Terranet\Options\ServiceProvider` is connected !before! the `Terranet\Administrator\ServiceProvider` in `config/app.php`.

To create Options module run `php artisan administrator:resource:settings`.

### Storage

To store application settings, terranet/options module needs a table:

Run the command `php artisan options:table` to create options table migration.

Now, run `php artisan migrate` to create the table.


### Manage settings

Add a setting by running `php artisan options:make <name> <value> [<group>]`

Remove a setting by running `php artisan options:remove <name>`

Enjoy!