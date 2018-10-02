<instant-search
        name="{{ $field->name() }}"
        data-url="/cms/search/?searchable={{ \App\User::class }}&field=name"
        default-value="{{ (int) (request('user_id') ?: optional($field->value())->getKey()) }}"
></instant-search>