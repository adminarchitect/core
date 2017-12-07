## Form Validation

In order to validate the create/edit forms, Admin Architect provides a `Validable` interface with a single required method `rules`.

Adding validation rules is nothing new, you can validate your forms, like you do it for regular Laravel Form Requests. 

Optionally you can define a `messages` method for custom validation messages. 

Let's say, your `app\Http\Terranet\Administrator\Modules\Users` module might have the following validation rules:
```php
public function rules()
{
	# rules, discovered by AA based on the table scheme.
	$discovered = $this->scaffoldRules();

	$minDate = '...';

	# add avatar validation rules.
    return array_merge($discovered, [
    	'birth_date' => "date|after:{$someDate}",
        'avatar' => 'image|dimensions:min_width=300,min_height=300|max:1024',
    ]);
}

# [optional]
public function messages()
{
	return [
		'avatar' => 'Can not process the :attribute. Please check the sizes.',
	];
}
```