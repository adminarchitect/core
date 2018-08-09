<?php
$attributes = [
    'class' => 'form-control'
];

if ($searchable) {
    $attributes += [
        'data-type' => 'livesearch',
        'data-url' => route('scaffold.search', ['searchable' => $related]),
        'placeholder' => 'Search...',
    ];
}
?>
@component('administrator::components.table.row')
    @slot('label', Form::label($field->id(), $field->title()))
    @slot('input')
        {!! Form::select($field->name(), $options, optional($field->value())->getKey(), $attributes) !!}
    @endslot
@endcomponent