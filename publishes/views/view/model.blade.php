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
        @if ($element instanceof \Terranet\Administrator\Collection\Group)
            <tr><th colspan="2" style="background: white;">&nbsp;</th></tr>
            <tr>
                <th colspan="2" class="btn-quirk">{{ $element->title() }}</th>
            </tr>
            @foreach($element->elements() as $element)
                <tr>
                    <td style="width: 20%; min-width: 200px;">{{ $element->title() }}</td>
                    <td>{!! $element->render('view') !!}</td>
                </tr>
            @endforeach
            <tr><th colspan="2" style="background: white;">&nbsp;</th></tr>
        @else
            <tr>
                <td style="width: 20%; min-width: 200px;">{{ $element->title() }}</td>
                <td>{!! $element->render('view') !!}</td>
            </tr>
        @endif
    @endforeach
</table>
