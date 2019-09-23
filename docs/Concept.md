# AdminArchitect Demo
### Installation
- [x] Explain the installation process
- [x] Mention possible workaround in case of private/custom repository

### adminarchitect-mix
- [x] installation, configuration processes
- [x] Available editors

### Concept
<a href='Concept.pdf'>Concept.pdf</a>

- [x] Explain basic architecture & concept:
AdminArchitect Core -> 
Routing -> 
Controller -> 
AdminRequest -> 
Resource -> 
Model -> 
Services -> 
- [x] Columns
- [x] Forms

### Explain Fields
- [x] Basic fields: Text -> Textarea -> Email -> Link -> Phone -> Date -> DateTime
- [x] WYSIWYG editors
- [x] Translatable
- [x] Attachments -> Paperclip 
- [x] Solve Traits collision between Translatable & Paperclip
- [x] Media -> Laravel Media Library
- [x] Enums - DB
- [x] Enumerable objects

### Relations
- [x] BelongsTo
- [x] HasOne
- [x] BelongsToMany

### Custom fields
- [x] Example

### Filters
- [x] Customisations
- [x] Queryable filters

### Scopes
- [x] Define scope
- [x] Customisations (name, icon, etc)

### Sortable
- [x] Define
- [x] Queryable

### Validation
- [x] Rules
- [x] Messages

### Finder
- [x] Explanation
- [x] Customisations Example
- [x] Command
`php artisan administrator:finder ResourceName`

### Saver
- [x] Explanation
- [x] Command
`php artisan administrator:saver ResourceName`

### Actions
- [x] Single
- [x] Batch
- [x] Authorization
- [x] Commands
```bash
php artisan administrator:actions ResourceName
php artisan administrator:action ActionName
php artisan administrator:action ActionName --batch
```

### CRUD Authorisation Policy
- [x] SuperAdminRule
- [x] Policy
`php artisan make:policy PlacePolicy`

### Cards & Widgets
- [x] Cards
- [x] Widgets
- [x] Command
`php artisan administrator:panel PanelName`

### Breadcrumbs
- [x] Explanation

### Template
- [x] Explanation

### Dashboard
- [x] Panels

### Navigation
- [x] Sidebar
- [x] Tools
- [x] Show/Hide resources
- [x] Customise Resources: Title, Url, Group, Navigation, Icon

### Media storage
- [x] Demo

### Links
[AdminArchitect - adminarchitect/core](https://github.com/adminarchitect/core)
[Admin Options - adminarchitect/options](https://github.com/adminarchitect/options)
[Admin Navigation - adminarchitect/navigation](https://github.com/adminarchitect/navigation)
[Translatable - TerranetMD/translatable](https://github.com/TerranetMD/translatable)
[Attachments - czim/laravel-paperclip](https://github.com/czim/laravel-paperclip)
[Media Library - spatie/laravel-medialibrary](https://github.com/spatie/laravel-medialibrary)
[Enumerable - BenSampo/laravel-enum](https://github.com/BenSampo/laravel-enum)
[Vessel - Docker dev environments for Laravel](https://vessel.shippingdocker.com/)
[Laravel Homestead](https://laravel.com/docs/5.8/homestead)
[Laravel Valet](https://laravel.com/docs/5.8/valet)
[Laravel Envoy - Common tasks to run on remote server](https://laravel.com/docs/5.8/envoy)
[Laravel Analytics - spatie/laravel-analytics](https://github.com/spatie/laravel-analytics)
[Open source | Spatie](https://spatie.be/open-source)
[The League of Extraordinary Packages](https://thephpleague.com/)
[Packagist](https://packagist.org/)


