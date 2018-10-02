<?php
$attributes = [
    'id' => $field->id(),
    'class' => 'img-responsive',
    'style' => 'max-width: 400px; max-height: 400px;',
];
?>
{!! \admin\output\staplerImage($attachment, 'original', $attributes) !!}