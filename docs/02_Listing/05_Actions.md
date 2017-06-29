## Actions

Admin Architect provides 2 types of actions: Single (applyed to every single element in a collection) and Batch (applyed to a collection of elements).


* * *

Example:

* Edit/Delete/View - are single actions.
* Delete Selected - is a batch action.

### Single Actions

![Single actions](http://docs.adminarchitect.com/docs/images/index/single_actions.jpg)

All CRUD (Create, Read, Update, Delete) actions are enabled out of the box for every single record in a resource collection.

All of these actions can be enabled or disabled!

Sometimes you'll need to have access to something more then just CRUD actions.

For instance: maybe you'll wish to activate or lock some users, view project reports, report emails as spam, etc...

Admin Architect Actions gives you ability to create a callback containers which receives as a parameter selected model, at this moment you are free to use the model in the way you need.

To extend default CRUD actions, just create a `Resource Actions Container`:

*Note! We assume that our Resource name is Posts, so we call our Action Container Posts also, overwise we have to set a Posts::$action property to our Action Container class:*

```
php artisan administrator:actions Posts
```

Admin Architect will generate new `Action` class for your resource located by default in `App\Http\Terranet\Administrator\Actions`

Our Actions\Posts has 2 method out of the box:

* actions() - returns a list of Single Actions
* batchActions() - returns a list of Batch Actions

Now, let's create a new single action:

```
php artisan administrator:action ToggleActiveStatus
```

then add this new created action to your Posts action container:

```
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

```
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
        # only super admins or post owners can toggle posts's active status.
        return $user->isSuperAdmin() || ($user->id == $entity->user_id);
    }
}
```

### CRUD Authorisation

Admin Architect provides a simple way to organize authorization logic and control access to resources.

The simplest way to determine if a user may perform a given CRUD action is to define an "ability" declaring the `can` method.

Within our `abilities`, we will determine if the logged in user has the ability to delete, update, view post:

```
### Actons\Posts::class

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

### Batch Actions

![Batch actions](http://docs.adminarchitect.com/docs/images/index/batch_actions.jpg)

Along of single actions Admin Architect provides a very simple way to manage collections of items.

Every resource has default ability (batch action) as "Remove selected items".

But, You are free to add your batch actions as like other single action, just running:
```
 php artisan administrator:action --batch ToggleSelected
```

Admin Architect will generate a sample Batch Actions class:

```
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
        //

        return $entity;
    }
}
```

As like Single Actions, Batch Actions have the same set of methods to customize it.

The difference between Single actions and Batch actions is that Batch ::handle() method receives a collection of elements, rather then Single Action receives a single entity.