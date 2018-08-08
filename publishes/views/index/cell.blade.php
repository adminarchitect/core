@if($column instanceof \Terranet\Administrator\Collection\Group)
    <ul class="list-unstyled">
        @foreach($column->elements() as $element)
            @if($value = $element->setModel($item)->render())
                <li>
                    @if ($element->isHiddenLabel())
                        <strong>{!! $value !!}</strong>
                    @else
                        <label for="{{ $element->id() }}">{{ $element->title() }}:</label>
                        {!! $value !!}
                    @endif
                </li>
            @endif
        @endforeach
    </ul>
@else
    {!! $column->setModel($item)->render() !!}
@endif