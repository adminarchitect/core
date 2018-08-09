@if ($value = $field->value())
    <a href="tel:{{$value}}">{{$value}}</a>
@endif