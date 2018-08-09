<?php
$attributes = [
    'id' => $field->id(),
    'class' => 'img-responsive',
    'style' => 'max-width: 400px; max-height: 400px;',
];
?>
@component('administrator::components.table.row')
    @slot('label', Form::label($field->id(), $field->title()))
    @slot('input')
        {!! \admin\output\staplerImage($attachment, 'original', $attributes) !!}
    @endslot
@endcomponent