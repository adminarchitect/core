@inject('module', 'scaffold.module')

<?php
$elements = $module->viewColumns($item);
?>
<table class="table table-striped-col">
    <tr>
        <th colspan="{{ $elements->count() }}" class="btn-quirk">
            {{ (isset($title) ? $title: $module->singular()) }}
        </th>
    </tr>
    @foreach($elements as $element)
        @if ($element instanceof \Terranet\Administrator\Form\FormSection)
            <tr>
                <th colspan="2" class="btn-quirk">{{ $element->title() }}</th>
            </tr>
        @elseif ($element instanceof \Terranet\Administrator\Columns\MediaElement)
            <tr>
                <td style="width: 20%; min-width: 200px;">{{ $element->title() }}</td>
                <td>
                    {!! $element->render($item) !!}
                </td>
            </tr>
        @else
            @if (! (is_array($value = $element->render($item)) || is_object($value)) || $value instanceof \Carbon\Carbon)
                <tr>
                    <td style="width: 20%; min-width: 200px;">{{ $element->title() }}</td>
                    <td>{!! $value !!}</td>
                </tr>
            @endif
        @endif
    @endforeach
</table>
