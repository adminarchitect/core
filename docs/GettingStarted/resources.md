## Resources

**Every Admin Architect resource by default corresponds to an Eloquent model. So before creating a resource you must first create an Eloquent model for it.**

### Create a Resource

Resource is the main container of all services provided by Admin Architect out of the box.
Resources handle Index Screens, Filters & Scopes, Create/Edit forms, Single & Batch Actions, Views, Exports and many other features.

The basic command for creating a resource is:

```bash
php artisan administrator:resource <name> <model>
```

Admin Architect will generate a new resource class in `app/Http/Terranet/Administrator/Modules` directory.

So your first Users resource might look so:

```php
namespace App\Http\Terranet\Administrator\Modules;

use App\User;
use Terranet\Administrator\Scaffolding;
use Terranet\Administrator\Traits\Module\HasForm;
use Terranet\Administrator\Traits\Module\HasFilters;
use Terranet\Administrator\Traits\Module\HasSortable;
use Terranet\Administrator\Traits\Module\AllowFormats;
use Terranet\Administrator\Traits\Module\ValidatesForm;
use Terranet\Administrator\Contracts\Module\Editable;
use Terranet\Administrator\Contracts\Module\Sortable;
use Terranet\Administrator\Contracts\Module\Filtrable;
use Terranet\Administrator\Contracts\Module\Navigable;
use Terranet\Administrator\Contracts\Module\Validable;
use Terranet\Administrator\Contracts\Module\Exportable;

class Users extends Scaffolding implements Navigable, Filtrable, Editable, Validable, Sortable, Exportable
{
    use HasFilters, HasForm, HasSortable, ValidatesForm, AllowFormats;

    protected $model = User::class;
}
```

### Interfaces
 - `Navigable` - makes the resource available in navigation (Sidebar, Tools).
 - `Filtrable` - generates _Filters_ & _Scopes_ based on Eloquent data.
 - `Editable` - generates edit/create forms.
 - `Validable` - enables Laravel validation support.
 - `Sortable` - provides grid columns sorting methods.
 - `Exportable` - exports collection to differrent formats: XML, CSV, PDF, etc...
