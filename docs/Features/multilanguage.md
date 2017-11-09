## Localization & Multilingual Support

In order to build multilingual applications you'll need a `terranet/localizer` & `terranet/translatable` packages (available for free).

Note: After installing it make sure that `Terranet\Localizer\ServiceProvider` and `Terranet\Translatable\Terranet\Translatable\TranslatableServiceProvider` are connected before the `Terranet\Administrator\ServiceProvider` in `config/app.php`.


!!! No one documentation can explain better then an example: let's create a translatable Posts module, migrations for this particular case are:

Create languages migration in case you didn't do it before:

```bash
php artisan languages:table
```

Create new languages module (App\Http\Terranet\Administrator\Modules\Settings).

```bash
php artisan administrator:resource:languages
```

Create a new migration for multilingual posts:

```bash
php artisan make:migration create_posts_table
```

Now, update your new created migration to look like:

```php
class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');

			$table->unsignedInteger('user_id')->nullable();
            $table
				->foreign('user_id')
				->references('id')
				->on('users')
				->onDelete('cascade')->onUpdate('cascade');

            $table->boolean('active');
            $table->timestamps();
        });

        Schema::create('post_translations', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('post_id')->nullable();
            $table
				->foreign('post_id')
				->references('id')
				->on('posts')
				->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('language_id')->nullable();
            $table
				->foreign('language_id')
				->references('id')
				->on('languages')
				->onDelete('cascade')->onUpdate('cascade');

            $table->string('title')->unique();
            $table->text('body')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_translations');
        Schema::dropIfExists('posts');
    }
}
```

Apply migrations

```bash
php artisan migrate
```

Now make your Post model `translatable`, by adding  `Terranet\Translatable\Translatable` interface and using `Terranet\Translatable\HasTranslations` trait.

Also define the property called `$translatedAttributes` to set `translatable` attributes:

```
class Post extends Model implements Translatable
{
    use HasTranslations;

    protected $fillable = ['user_id', 'active'];

    protected $translatedAttributes = ['title', 'body'];

    public function meta()
    {
        return $this->hasOne(PostMeta::class);
    }
}

# your PostTranslation model
class PostTranslation extends Model
{
    public $timestamps = false;
}
```

Now, our edit form will have a look like this:
![Overview](http://docs.adminarchitect.com/images/plugins/multilingual.png)

As you might see, any `translatable` attribute has language switch box.
Also all `translatable` attributes are available in `columns` grid.