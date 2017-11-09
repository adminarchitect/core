## Actions

Admin Architect provides 2 action types: 
- Single (applied to every single element in a collection) 
- Batch (applied to a collection of elements).

To understand better, here are some examples:
* Edit/Delete/View - are examples of single actions.
* Check More && Delete Selected Items - is a batch action.

* * *

### Single (Row-Based) Actions

![Admin Architect - Single actions](http://docs.adminarchitect.com/images/index/single_actions.jpg)

CRUD (Create, Read, Update, Delete) actions are enabled out of the box for every single record in a resource collection.

All of these actions can be enabled or disabled (We'll see later, so stay in touch)!

Sometimes you'll need to have something more then just CRUD actions, or maybe you won't need the View action, etc...

For instance: maybe you'll wish to `activate` or `lock` specific users, view project reports, report emails as spam, etc...

Admin Architect gives you ability to create the action containers (collections) which receive as a callback parameter selected model, at this moment you are free to use the model in the way you need.

### CRUD Authorisation

Admin Architect provides a simple way to organize authorization logic and control access to resources.
We'll review few use cases of how you can organize your Authorization logic.

#### Abilities

The very first way to determine if a user may perform a given CRUD action is to define an "ability" declaring the `can` method.

Within our `abilities`, we will determine if the logged in user has the ability to delete, update, view post:

For this purpose we'll need to have the these `abilities` defined in our `Actions` service (See [Create Actions](/Listing/actions?id=create-actions) section).

```php
# Actons\Posts::class

/**
 * @param $user - Logged in user
 * @param $entity - Eloquent model you're going to Delete/Update/View/etc...
 */
public function canDelete($user, $entity)
{
    return $user->isSuperAdmin() || $user->isOwnerOf($entity);
}

public function canUpdate($user, $entity)
{
    return $this->canDelete($user, $entity);
}

public function canView($user, $entity)
{
    return $this->canDelete($user, $entity);
}
```

#### ACL Manager 

The second and more general & powerful method is to create a `GuardManager` class and register it in the `config/administrator.php`...

Let's see an example:

```php
# administrator.php
'acl' => [
    'manager' => \App\Services\GuardManager::class, 
],
```

```php
# App\Services\GuardManager.php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Terranet\Administrator\Scaffolding;

class GuardManager
{
    protected $module = null;

    public function __construct(Scaffolding $module)
    {
        $this->module = $module;
    }

    public function canCreate(Authenticatable $user)
    {
        return $user->can(
            $this->permission('create')
        );
    }

    public function canUpdate(Authenticatable $user, $eloquent)
    {
        return $user->can(
            $this->permission('update'),
            $eloquent
        );
    }

    public function canDelete(Authenticatable $user, $eloquent)
    {
        return $user->can(
            $this->permission('delete'),
            $eloquent
        );
    }

    public function canView(Authenticatable $user, $eloquent)
    {
        return in_array($this->module->url(), ['users', 'offers']);
    }

    public function canIndex(Authenticatable $user)
    {
        return $user->can(
            $this->permission('index')
        );
    }

    public function showIf()
    {
        return $this->canIndex(
            $this->user()
        );
    }

    protected function user(): Authenticatable
    {
        return auth('admin')->user();
    }

    protected function permission($permission)
    {
        return $this->module->url() . '.' . $permission;
    }
}

# Authenticable::can() method can realize any logic inside, it can have the `zizaco/entrust` logic or something similar or more complex, it just must return true or false (does user can perform the action or not).
# $permission - string representation of <module>.<action>, like: users.index, users.create, users.edit
# it also receives a current $eloquent model as a second argument.
```

#### Alternative way

As an alternative way, to enable/disable CRUD action in a global aspect, there is a simple tricky way: 
open your `AppServiceProvider` and add this to `boot()` method:

```php
# available CRUD actions: canView, canDelete, canUpdate, canCreate
Scaffolding::addMethod('canView', function ($user, $eloquent) {
    # let's enable View action for Users module
    if (in_array(app('scaffold.module')->url(), ['users'])) {
        return true;
    }

    # and disable for others
    return false;
});

# Ex.: Only Admins and Managers are able to Update some row.
# @param $user - logged in user
# @param $eloquent - the model you try to update
Scaffolding::addMethod('canUpdate', function($user, $eloquent) {
    return $user->hasRole(['admin', 'manager']);
});
```

### Create Actions

As we said you can add your own actions, for this you have to create a `Actions Container`:

*Note! We assume that our Resource name is Posts, so we call our Action Container Posts also, overwise we have to set a Posts::$action property to our Action Container class:*

```bash
php artisan administrator:actions Posts
```

Admin Architect will generate new `Action` container class for your resource located by in `App\Http\Terranet\Administrator\Actions`

New generated `Actions\Posts` will have 2 method out of the box:

* actions() - returns a list of Single Actions
* batchActions() - returns a list of Batch Actions

Now, let's create a new single action:

```bash
php artisan administrator:action ToggleActiveStatus
```

then add this action to your Posts::actions() array:

```php 
class Posts extends CrudActions
{
    public function actions()
    {
        return [
            ToggleActiveStatus::class
        ];
    }
}
```

then you'll see a new action called "Toggle active status" along every single row in your Posts list.

Now, it's time to customize our ToggleActiveStatus::class and add callback handler and authorization logic.

```php
class ToggleActiveStatus
{
    use Skeleton, ActionSkeleton;

	/**
	 * Set action name.
	 *
	 * @param Eloquent $entity
	 * @return string
	 */
    public function name(Eloquent $entity)
    {
        return $entity->active ? 'Lock' : 'Activate';
    }

    /**
     * Switch post activity status.
     *
     * @param Eloquent $entity
     * @return mixed
     */
    public function handle(Eloquent $entity)
    {
        $entity->active = !$entity->active;
        $entity->save();

        return $entity;
    }

	/**
	 * Set authorization logic.
	 * Define who is authorized to manage this action.
	 *
	 * @param User $user
	 * @param Eloquent $entity
	 * @return boolean
	 */
    public function authorize(User $user, Eloquent $entity)
    {
        # let's say: only super admins or post owners can toggle posts's active status.
        return $user->isSuperAdmin() || ($user->id == $entity->user_id);
    }
}
```

### Batch Actions

![Admin Architect - Batch actions](http://docs.adminarchitect.com/images/index/batch_actions.jpg)

Along with single actions Admin Architect provides a very simple way to manage collections of items.

Every resource has default ability (batch action) as "Remove selected items".

But, You are free to add your batch actions as like other single action, just running:

```bash
 php artisan administrator:action --batch ToggleSelected
```

Admin Architect will generate a sample Batch Action class, which can be updated to something like:

```php
class ToggleSelected
{
    use Skeleton, BatchSkeleton;

    /**
     * Perform a batch action.
     *
     * @param Eloquent $entity
     * @param array $collection
     * @return mixed
     */
    public function handle(Eloquent $entity, array $collection = [])
    {
        User::whereIn($collection->pluck('id'))->update([
            'active' => \DB::raw('!active')
        ]);

        return $entity;
    }
}
```

As like Single Actions, Batch Actions have the same set of methods to control them.

The difference between Single actions and Batch actions is that `Batch::handle()` method receives a collection of elements, rather then Single Action receives a single entity.