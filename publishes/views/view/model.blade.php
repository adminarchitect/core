@inject('module', 'scaffold.module')

<?php
$elements = $module->viewColumns()->each->setModel($item);
?>
<table class="table table-striped-col">
    <tr>
        <th colspan="{{ $elements->count() }}" class="btn-quirk">
            {{ (isset($title) ? $title: $module->singular()) }}
        </th>
    </tr>
    @foreach($elements as $element)
        @if ($element instanceof \Terranet\Administrator\Collection\Group)
            <tr>
                <th colspan="2" style="background: white;">&nbsp;</th>
            </tr>
            <tr>
                <th colspan="2" class="btn-quirk">{{ $element->title() }}</th>
            </tr>
            @foreach($element->elements() as $element)
                <tr>
                    <td style="width: 20%; min-width: 200px;">{{ $element->title() }}</td>
                    <td>{!! $element->render('view') !!}</td>
                </tr>
            @endforeach
            <tr>
                <th colspan="2" style="background: white;">&nbsp;</th>
            </tr>
        @elseif ($element instanceof \Terranet\Administrator\Field\HasMany)
            @continue
        @else
            <tr>
                <td style="width: 20%; min-width: 200px;">{{ $element->title() }}</td>
                <td>{!! $element->render('view') !!}</td>
            </tr>
        @endif
    @endforeach
</table>

@foreach($elements as $element)
    @if ($element instanceof \Terranet\Administrator\Field\HasMany)
        @if ($output = $element->render(\Terranet\Administrator\Scaffolding::PAGE_VIEW))
            <table class="table">
                <tr>
                    <th colspan="2" style="background: white;">&nbsp;</th>
                </tr>
                <tr>
                    <th colspan="2" class="btn-quirk">{{ $element->title() }}</th>
                </tr>
            </table>
            {!! $output !!}
        @endif
    @endif
@endforeach
