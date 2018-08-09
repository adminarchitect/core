<?php
$attributes = [
    'id' => $field->id(),
    'class' => 'img-circle',
    'width' => 60,
    'height' => 60,
];
?>
{!! \admin\output\staplerImage($attachment, 'original', $attributes) !!}